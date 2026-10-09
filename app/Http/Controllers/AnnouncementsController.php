<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\CouponCode;
use App\Models\Announcement;
use App\Models\NetworkIssueAlert;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AnnouncementsController extends Controller
{
    public function index(){
       
        // dd('asdfsdfsss');
        $ann = Announcement::get();
        $data['announcements'] = $ann;
        $data['networks'] = Network::orderBy('network_name')->get();
        $data['networkIssueAlerts'] = NetworkIssueAlert::with('network')
            ->orderBy('priority')
            ->orderByDesc('is_active')
            ->get()
            ->keyBy('network_id');
        return view('admin.announcements.index')->with($data);
    }

    public function store(Request $request){

          $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'position' => 'required',
            'status' => ['required',Rule::in(['1','0'])],
          ]);
    
          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }


          $data['title'] = $request->title;
          $data['description'] = $request->description;
          $data['position'] = $request->position;
          $data['status'] = 1;
          $ann = Announcement::create($data);

          Session::flash('success','Customer announcement was successfully added');
          return redirect()->back();
    }

    public function update($id, Request $request){

        $validator = Validator::make($request->all(), [
          'title' => 'required',
          'description' => 'required',
          'position' => 'required',
          'status' => ['required',Rule::in(['1','0'])],
        ]);
  
        if ($validator->stopOnFirstFailure()->fails()) {
          return redirect()->back()->withErrors($validator)->withInput();
        }

        // dd($request->all());

        $ann_check = Announcement::where('id',$id)->first();
        if(! $ann_check){
            Session::flash('failure','This announcement is not found');
            return redirect()->back();
        }


        $data['title'] = $request->title;
        $data['description'] = $request->description;
        $data['position'] = $request->position;
        $data['status'] = $request->status;
        $ann_check->update($data);

        Session::flash('success','Customer announcement was successfully added');
        return redirect()->back();
  }

    public function updateNetworkIssueAlert(Request $request)
    {
        $validated = $request->validate([
            'network_id' => ['required', 'exists:networks,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', Rule::in(['1', '0'])],
            'priority' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        NetworkIssueAlert::updateOrCreate(
            ['network_id' => $validated['network_id']],
            [
                'title' => $validated['title'] ?: null,
                'message' => $validated['message'] ?: null,
                'is_active' => $validated['is_active'] === '1',
                'priority' => $validated['priority'] ?? 1,
            ]
        );

        Session::flash('success', 'Network issue alert updated successfully');

        return redirect()->back();
    }
}
