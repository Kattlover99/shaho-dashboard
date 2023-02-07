<?php

namespace App\Http\Controllers;

use App\ContactCategory;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ContactCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('contact_category_view') && !auth()->user()->can('contact_category_add')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $category = ContactCategory::where('business_id', $business_id)
                        ->select(['name', 'description', 'id']);

            return Datatables::of($category)
                ->addColumn(
                    'action',
                    '@can("contact_category_update")
                    <button data-href="{{action(\'ContactCategoryController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("contact_category_delete")
                        <button data-href="{{action(\'ContactCategoryController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }

        return view('contact_category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('contact_category_add')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $categories = ContactCategory::where('business_id', $business_id)
                        ->where('parent_id', 0)
                        ->select(['name', 'short_code', 'id'])
                        ->get();
        $parent_categories = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $parent_categories[$category->id] = $category->name;
            }
        }
        return view('contact_category.create')
                    ->with(compact('parent_categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('contact_category_add')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'description']);
            if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                $input['parent_id'] = $request->input('parent_id');
            } else {
                $input['parent_id'] = 0;
            }
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            $category = ContactCategory::create($input);
            $output = ['success' => true,
                            'data' => $category,
                            'msg' => __("category.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(ContactCategory $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('contact_category_update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $category = ContactCategory::where('business_id', $business_id)->find($id);
            
            $parent_categories = ContactCategory::where('business_id', $business_id)
                                        ->where('parent_id', 0)
                                        ->where('id', '!=', $id)
                                        ->pluck('name', 'id');
            
            $is_parent = false;
            
            if ($category->parent_id == 0) {
                $is_parent = true;
                $selected_parent = null;
            } else {
                $selected_parent = $category->parent_id ;
            }

            return view('contact_category.edit')
                ->with(compact('category', 'parent_categories', 'is_parent', 'selected_parent'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		
        if (!auth()->user()->can('contact_category_update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'description']);
                $business_id = $request->session()->get('user.business_id');

                $category = ContactCategory::where('business_id', $business_id)->findOrFail($id);
                $category->name = $input['name'];
                $category->description = $input['description'];
                if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                    $category->parent_id = $request->input('parent_id');
                } else {
                    $category->parent_id = 0;
                }
                $category->save();

                $output = ['success' => true,
                            'msg' => __("category.updated_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('contact_category_delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

	            $count = Product::where('category_id', '=', $id)
		            ->where('business_id', '=', $business_id)
		            ->count();

	            if ($count > 0) {
		            $output = ['success' => false,
			            'msg' => __("category.can_not_delete")
		            ];
	            } else {
		            $category = ContactCategory::where('business_id', $business_id)->findOrFail($id);
		            $category->delete();

		            $output = ['success' => true,
			            'msg' => __("category.deleted_success")
		            ];
	            }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }
}
