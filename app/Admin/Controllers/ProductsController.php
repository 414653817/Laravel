<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductsController extends Controller
{
    use HasResourceActions;

    //商品列表
    public function index(Content $content)
    {
        return $content
            ->header('商品列表')
            ->description('商品列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
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
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑商品')
            ->body($this->form()->edit($id));
    }


    public function create(Content $content)
    {
        return $content
            ->header('创建商品')
            ->body($this->form());
    }


    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->id('ID')->sortable();
        $grid->title('商品名称');
        $grid->on_sale('是否上架')->display(function($value) {
            return $value ? '是' : '否';
        });

        $grid->price('价格');
        $grid->rating('评分');
        $grid->sold_count('销量');
        $grid->review_count('评论数');

        $grid->created_at('创建时间');

        $grid->actions(function($actions) {
            //$actions->disableView();
            $actions->disableDelete();
        });

        //取消批量删除
        $grid->tools(function($tools) {
            $tools->batch(function($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->id('ID');
        $show->title('商品名称');
        $show->description('商品描述');
        $show->image('封面图片')->image();
        $show->on_sale('是否上架')->as(function($on_sale) {
            if($on_sale) {
                return '是';
            }else {
                return '否';
            }
        });
        $show->rating('评价')->badge();
        $show->sold_count('库存');
        $show->review_count('评分');
        $show->price('价格');
        $show->created_at('创建时间');

        //sku信息
        $show->skus('SKU 信息', function ($sku) {

            //此$sku是一个grid实例
            $sku->resource('/admin/products');

            $sku->id();
            $sku->title();
            $sku->description();
            $sku->price();
            $sku->stock();
            $sku->created_at();
            $sku->updated_at();

            /*$sku->actions(function($actions) {
                $actions->disableView();
                $actions->disableDelete();
                $actions->disableEdit();
            });*/
            $sku->disableActions();

            //取消批量删除
            $sku->tools(function($tools) {
                $tools->batch(function($batch) {
                    $batch->disableDelete();
                });
            });

        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);

        // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
        $form->text('title', '商品标题')->rules('required');
        //图片上传框
        $form->image('image', '封面图片')->rules('required|image');

        //富文本编辑器(此组件被禁用了需要在app/admin/bootstrap.php)
        $form->editor('description', '详细描述')->rules('required');
        //单选框
        $form->radio('on_sale', '上架')->options(['1'=>'是','0'=>'否'])->default('0');

        //直接添加一对多的关联模型
        $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
            $form->text('title', 'SKU 名称')->rules('required');
            $form->text('description', 'SKU 描述')->rules('required');
            $form->text('price', '单价')->rules('required|numeric|min:0.01');
            $form->text('stock', '剩余库存')->rules('required|integer|min:0');
        });

        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            //当我们在前端移除一个 SKU 的之后，点击保存按钮时 Laravel-Admin 仍然会将被删除的 SKU 提交上去，
            //但是会添加一个 _remove_=1(正常为0) 的字段
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;

        });

        return $form;
    }
}
