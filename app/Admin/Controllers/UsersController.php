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

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('User list')
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

        $grid->id('Id');

        $grid->name('Name');

        $grid->email('Email');

        $grid->email_verified_at('Email has been verified?')->display(function ($value) {
            return $value ? 'Yes' : 'No';
        });

        $grid->created_at('Register date');

        $grid->disableCreateButton();

       $grid->actions(function ($actions) {

            $actions->disableView();

            $actions->disableDelete();

            $actions->disableEdit();
        });

       $grid->tools(function ($tools) {

            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }


}
