<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrdersController extends Controller
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
            ->header('Order List')
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
            ->header('Edit')
            ->description('description')
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
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        // Show only orders that have already been paid, and sort by default in reverse order of payment time
        $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');

        $grid->no('Order No');
        // Use the column method when presenting the fields of an association
        $grid->column('user.name', 'Buyer');
        $grid->total_amount('Total Amount')->sortable();
        $grid->paid_at('Payment time')->sortable();
        $grid->ship_status('Shipment')->display(function($value) {
            return Order::$shipStatusMap[$value];
        });
        $grid->refund_status('Refund Status')->display(function($value) {
            return Order::$refundStatusMap[$value];
        });
        // Disable the create button, the background does not need to create an order
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // Disable delete and edit buttons
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->tools(function ($tools) {
            // Disable bulk delete button
            $tools->batch(function ($batch) {
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
        $show = new Show(Order::findOrFail($id));

        $show->id('Id');
        $show->no('No');
        $show->user_id('User id');
        $show->address('Address');
        $show->total_amount('Total amount');
        $show->remark('Remark');
        $show->paid_at('Paid at');
        $show->payment_method('Payment method');
        $show->payment_no('Payment no');
        $show->refund_status('Refund status');
        $show->refund_no('Refund no');
        $show->closed('Closed');
        $show->reviewed('Reviewed');
        $show->ship_status('Ship status');
        $show->ship_data('Ship data');
        $show->extra('Extra');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->text('no', 'No');
        $form->number('user_id', 'User id');
        $form->textarea('address', 'Address');
        $form->decimal('total_amount', 'Total amount');
        $form->textarea('remark', 'Remark');
        $form->datetime('paid_at', 'Paid at')->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', 'Payment method');
        $form->text('payment_no', 'Payment no');
        $form->text('refund_status', 'Refund status')->default('pending');
        $form->text('refund_no', 'Refund no');
        $form->switch('closed', 'Closed');
        $form->switch('reviewed', 'Reviewed');
        $form->text('ship_status', 'Ship status')->default('pending');
        $form->textarea('ship_data', 'Ship data');
        $form->textarea('extra', 'Extra');

        return $form;
    }
}
