<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends Controller
{
    use HasResourceActions;


    public function index(Content $content)
    {
        return $content
            ->header('用户列表')
            ->description('用户列表')
            ->body($this->grid());
    }



    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->id('ID')->sortable();
        $grid->name('用户名');
        $grid->email('邮箱');
        $grid->email_verified_at('已验证邮箱')->display(function($value) {
            return $value ? '是' : '否';
        });
        $grid->password('密码');
        $grid->remember_token('Remember token');
        $grid->created_at('创建时间');
        $grid->updated_at('修改时间');

        //不显示创建按钮
        $grid->disableCreateButton();
        //每行不显示操作，查看编辑删除等
        /*$grid->actions(function($action) {
            $action->disableView();
            $action->disableEdit();
            $action->disableDelete();
        });*/
        $grid->disableActions();  //禁用行操作列

        $grid->tools(function($tools) {
            $tools->batch(function($batch) {
                $batch->disableDelete();
            });
        });
        
        return $grid;
    }

}
