<?php

namespace App\Admin\Controllers;

use App\Models\CouponCode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CouponCodesController extends Controller
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
            ->header('Coupon List')
            ->body($this->grid());
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
            ->header('Edit')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create New Coupon')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CouponCode);

        $grid->model()->orderBy('created_at', 'desc');
        $grid->id('ID')->sortable();
        $grid->name('title');
        $grid->code('coupon code');
        $grid->description('description');
        $grid->column('usage', 'usage')->display(function ($value) {
            return "{$this->used} / {$this->total}";
        });
        $grid->enabled('Enabled')->display(function ($value) {
            return $value ? 'Yes' : 'No';
        });
        $grid->created_at('Created At');
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        return $grid;
    }



    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CouponCode);

        $form->display('id', 'ID');
        $form->text('name', 'Name')->rules('required');
        $form->text('code', 'CouponCode')->rules(function($form) {
            // If $form->model()->id is not empty, it means an edit operation
            if ($id = $form->model()->id) {
                return 'nullable|unique:coupon_codes,code,'.$id.',id';
            } else {
                return 'nullable|unique:coupon_codes';
            }
        });
        $form->radio('type', 'Type')->options(CouponCode::$typeMap)->rules('required')->default(CouponCode::TYPE_FIXED);
        $form->text('value', 'Discount Value')->rules(function ($form) {
            if (request()->input('type') === CouponCode::TYPE_PERCENT) {
                // If the percentage discount type is selected, the discount range can only be 1 ~ 99
                return 'required|numeric|between:1,99';
            } else {

                return 'required|numeric|min:0.01';
            }
        });
        $form->text('total', 'total')->rules('required|numeric|min:0');
        $form->text('min_amount', 'Minimun Amount')->rules('required|numeric|min:0');
        $form->datetime('not_before', 'Start Time');
        $form->datetime('not_after', 'End Time');
        $form->radio('enabled', 'Enabled')->options(['1' => 'Yes', '0' => 'No']);

        $form->saving(function (Form $form) {
            if (!$form->code) {
                $form->code = CouponCode::findAvailableCode();
            }
        });

        return $form;
    }
}
