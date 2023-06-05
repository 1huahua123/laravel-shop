<?php

namespace App\Admin\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\Admin\HandleRefundRequest;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('订单列表');
            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return Admin::grid(Order::class, function (Grid $grid) {
            // 只展示已支付的订单，并且默认按支付时间倒序排序
            $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');

            $grid->no('订单流水号');
            // 展示关联关系的字段时，使用 column 方法
            $grid->column('user.name', '买家');
            $grid->total_amount('总金额')->sortable();
            $grid->paid_at('支付时间')->sortable();
            $grid->ship_status('物流')->display(function($value) {
                return Order::$shipStatusMap[$value];
            });
            $grid->refund_status('退款状态')->display(function($value) {
                return Order::$refundStatusMap[$value];
            });
            // 禁用创建按钮，后台不需要创建订单
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                // 禁用删除和编辑按钮
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

    /**
     * 订单详情页面
     * @param Order $order
     * @return Content
     */
    public function show(Order $order)
    {
        return Admin::content(function (Content $content) use ($order) {
           $content->header('查看订单');
           $content->body(view('admin.orders.show', ['order' => $order]));
        });
    }

    public function ship(Order $order, Request $request)
    {
        // 判断当前订单是否已支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未付款');
        }
        // 判断当前订单发货状态是否为未发货
        if ($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已发货');
        }
        // Laravel 5.5 之后 validate 方法可以返回校验过的值
        $data = $this->validate($request, [
            'express_company' => ['required'],
            'express_no'      => ['required'],
        ], [], [
            'express_company' => '物流公司',
            'express_no'      => '物流单号',
        ]);
        // 将订单发货状态改为已发货，并存入物流信息
        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            // 我们在 Order 模型的 $casts 属性里指明了 ship_data 是一个数组
            // 因此这里可以直接把数组传过去
            'ship_data'   => $data,
        ]);

        // 返回上一页
        return redirect()->back();
    }

    public function handleRefund(Order $order, HandleRefundRequest $request)
    {
        // 判断订单的状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
            throw new InvalidRequestException('订单状态不正确');
        }
        // 是否同意退款
        if ($request->input('agree')) {
            $this->_refundOrder($order);
        } else {
            // 将拒绝退款的理由放在 extra 字段中
            $extra = $order->extra ?: [];
            $extra['refund_disagree_reason'] = $request->input('reason');
            // 将订单状态更新为未退款，并且存储拒绝退款理由
            $order->update([
                'refund_status' => Order::REFUND_STATUS_PENDING,
                'extra' => $extra,
            ]);
        }

        return $order;

    }

    protected function _refundOrder(Order $order)
    {
        switch ($order->payment_method) {
            case 'wechat':
                $refundNo = Order::getAvailableRefundNo();
                app('wechat_pay')->refund([
                    'out_trade_no' => $order->no, // 之前的订单流水号
                    'total_fee' => $order->total_amount * 100, //原订单金额，单位分
                    'refund_fee' => $order->total_amount * 100, // 要退款的订单金额，单位分
                    'out_refund_no' => $refundNo, // 退款订单号
                    // 微信支付的退款结果并不是实时返回的，而是通过退款回调来通知，因此这里需要配上退款回调接口地址
                    'notify_url' => route('payment.wechat.refund_notify'), // 由于是开发环境，需要配成 requestbin 地址
                ]);
                // 将订单状态改成退款中
                $order->update([
                    'refund_no' => $refundNo,
                    'refund_status' => Order::REFUND_STATUS_PROCESSING,
                ]);
                break;
            case 'alipay':
                $refundNo = Order::getAvailableRefundNo();

                $ret = app('alipay')->refund([
                    'out_trade_no' => $order->no,
                    'refund_amount' => $order->total_amount,
                    'out_requst_no' => $refundNo,
                ]);

                if ($ret->sub_code) {
                    $extra = $order->extra;
                    $extra['refund_failed_code'] = $ret->sub_code;

                    $order->update([
                       'refund_no' => $refundNo,
                       'refund_status' => Order::REFUND_STATUS_FAILED,
                       'extra' => $extra,
                    ]);
                } else {
                    $order->update([
                       'refund_no' => $refundNo,
                       'refund_status' => Order::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;
            default:
                throw new InvalidRequestException('未知订单支付方式：'.$order->payment_method);
                break;
        }
    }
}
