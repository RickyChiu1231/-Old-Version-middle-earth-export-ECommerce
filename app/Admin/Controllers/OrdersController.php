<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\Admin\HandleRefundRequest;
use App\Exceptions\InternalException;


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
    public function show(Order $order, Content $content)
    {
        return $content
            ->header('Check Order detail')
            // body method can accept Laravel's view as a parameter
            ->body(view('admin.orders.show', ['order' => $order]));
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

    public function ship(Order $order, Request $request)
    {
        // determine the order has been paid or not
        if (!$order->paid_at) {
            throw new InvalidRequestException('The order did not pay');
        }
        // Determine whether the current order delivery status is not shipped
        if ($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('The order has been shipped');
        }

        $data = $this->validate($request, [
            'express_company' => ['required'],
            'express_no'      => ['required'],
        ], [], [
            'express_company' => 'Express Company',
            'express_no'      => 'Tracking No.',
        ]);
        // Change the order delivery status to shipped and save the logistics information
        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            'ship_data'   => $data,
        ]);

        // return to previous page
        return redirect()->back();
    }


    public function handleRefund(Order $order, HandleRefundRequest $request)
    {
        // Determine if the order status is correct
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
            throw new InvalidRequestException('Order status is incorrect');
        }
        // Agree to refund or not
        if ($request->input('agree')) {
            // Empty rejection of refund
            $extra = $order->extra ?: [];
            unset($extra['refund_disagree_reason']);
            $order->update([
                'extra' => $extra,
            ]);
            // Call the refund logic
            $this->_refundOrder($order);
        } else {
            // Place the reason for rejecting the refund in the extra field of the order
            $extra = $order->extra ?: [];
            $extra['refund_disagree_reason'] = $request->input('reason');
            // Change the refund status of the order to a non-refundable
            $order->update([
                'refund_status' => Order::REFUND_STATUS_PENDING,
                'extra'         => $extra,
            ]);
        }
        return $order;
    }



    protected function _refundOrder(Order $order)
    {
        // Determine the payment method of the order
        switch ($order->payment_method) {
            case 'wechat':
                // For wechat
                // todo
                break;
            case 'alipay':
                // Generate a refund order number using the method we just wrote
                $refundNo = Order::getAvailableRefundNo();
                // The method of invoking the Alipay payment instance
                $ret = app('alipay')->refund([
                    'out_trade_no' => $order->no, // Previous order serial number
                    'refund_amount' => $order->total_amount,
                    'out_request_no' => $refundNo, // Refund order number
                ]);
                // According to Alipay's documentation, if there is a sub_code field in the return value, the refund fails.
                if ($ret->sub_code) {
                    // Save the refund failed save to the extra field
                    $extra = $order->extra;
                    $extra['refund_failed_code'] = $ret->sub_code;
                    // Mark the refund status of the order as a refund failure
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => $extra,
                    ]);
                } else {
                    // Mark the refund status of the order as a refund and save the refund order number
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;
            default:
                throw new InternalException('Unknown order payment method：'.$order->payment_method);
                break;
        }
    }


}
