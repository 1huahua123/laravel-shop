<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductsController extends Controller
{
    use HasResourceActions;

    /**
     * @return mixed
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
           $content->header('商品列表');
           $content->body($this->grid());
        });
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('编辑商品');
            $content->body($this->form()->edit($id));
        });
    }


    /**
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
           $content->header('创建商品');
           $content->body($this->form());
        });
    }

    /**
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Product::class, function (Grid $grid) {
           $grid->id('ID')->sortable();
           $grid->title('商品名称');
           $grid->on_sale('已上架')->display(function ($value) {
              return $value ? '是' : '否';
           });
           $grid->price('价格');
           $grid->rating('评分');
           $grid->sold_count('销量');
           $grid->review_count('评论数');

           $grid->actions(function ($actions) {
              $actions->disableView();
              $actions->disableDelete();
           });
           $grid->tools(function ($tools) {
              $tools->batch(function ($batch) {
                 $batch->disableDelete();
              });
           });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->id('Id');
        $show->title('Title');
        $show->description('Description');
        $show->image('Image');
        $show->on_sale('On sale');
        $show->rating('Rating');
        $show->sold_count('Sold count');
        $show->review_count('Review count');
        $show->price('Price');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    protected function form()
    {
        // 创建一个表单
        return Admin::form(Product::class, function (Form $form) {
            // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
           $form->text('title', '商品名称')->rules('required');
            // 创建一个选择图片的框
           $form->image('image', '封面图片')->rules('required');
            // 创建一个富文本编辑器
           $form->editor('description', '商品描述')->rules('required');
            // 创建一组单选框
           $form->radio('on_sale', '上架')->options(['1' => '是', '0' => '否'])->default(0);
            // 直接添加一对多的关联模型
           $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
               $form->text('title', 'SKU 名称')->rules('required');
               $form->text('description', 'SKU 描述')->rules('required');
               $form->text('price', '单价')->rules('required|numeric|min:0.01');
               $form->text('stock', '剩余库存')->rules('required|integer|min:0');
           });
            // 定义事件回调，当模型即将保存时会触发这个回调
           $form->saving(function (Form $form) {
               $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
           });
        });
    }
}
