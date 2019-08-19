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

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('product list')
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
            ->header('Edit product')
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
            ->header('Create product')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->id('ID')->sortable();
        $grid->title('Product Name');
        $grid->on_sale('On Sale')->display(function ($value) {
            return $value ? 'Yes' : 'No';
        });
        $grid->price('Price');
        $grid->rating('Rating');
        $grid->sold_count('Sales');
        $grid->review_count('Number of Review');

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });
        $grid->tools(function ($tools) {

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

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);

        // Create an input box, the first parameter title is the field name of the model, and the second parameter is the field description
        $form->text('title', 'Product Name')->rules('required');

        // Create a box to select a picture
        $form->image('image', 'Product Image')->rules('required|image');

        // Create a text editor
        $form->editor('description', 'Product Description')->rules('required');

        // Create a set of radio buttons
        $form->radio('on_sale', 'On Sale')->options(['1' => 'Yes', '0'=> 'No'])->default('0');

        // Add one-to-many association models directly
        $form->hasMany('skus', 'SKU List', function (Form\NestedForm $form) {
            $form->text('title', 'SKU Name')->rules('required');
            $form->text('description', 'SKU description')->rules('required');
            $form->text('price', 'price')->rules('required|numeric|min:0.01');
            $form->text('stock', 'InStock')->rules('required|integer|min:0');
        });

        // Define an event callback that will be triggered when the model is about to be saved
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
        });

        return $form;
    }
}
