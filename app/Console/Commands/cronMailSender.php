<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\CronMailNotifications;
use App\JobOrderTrades;

class cronMailSender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronmail:sender';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron mail sending - 15 mins once';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $checking_pending_crons= CronMailNotifications::where('mail_status','0')->get();
        
        if(count($checking_pending_crons)>0){
            foreach($checking_pending_crons as $key=>$value){
                /*Tc Job Order Notifications*/
                if($value->mail_type=="1"){
                    $action_id=$value->action_id;
                    /*get job order trades details*/
                    $job_order_trades= JobOrderTrades::where('id',$action_id)->first();
                    $job_order_id=$job_order_trades->jobOrder->job_order_id;
                    $property=$job_order_trades->jobOrder->properties->property_name;
                    $location=$job_order_trades->jobOrder->location;
                    $commencement_date=date('d-m-Y',strtotime($job_order_trades->jobOrder->commencement_date));
                    $completion_date=date('d-m-Y',strtotime($job_order_trades->jobOrder->completion_date));
                    $due_date=date('d-m-Y',strtotime($job_order_trades->jobOrder->due_date));
                    $job_description=$job_order_trades->jobOrder->description;
                    $trade=$job_order_trades->jobOrderCategory->trade;
                    $scope_of_work=$job_order_trades->scope_of_work;
                    $myEmailId=$value->mail_id;
                    $subject="New Job Order - $job_order_id";
                    $mail_data=[
                        'job_order_id'=>$job_order_id,
                        'property'=>$property,
                        'location'=>$location,
                        'commencement_date'=>$commencement_date,
                        'completion_date'=>$completion_date,
                        'due_date'=>$due_date,
                        'job_description'=>$job_description,
                        'trade'=>$trade,
                        'scope_of_work'=>$scope_of_work
                    ];
                        DB::beginTransaction();
                        try {
                            Mail::send('mail_template.tc_job_order', ['summary' =>$mail_data], function ($m) use ($myEmailId, $subject) {
                                $m->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                                $m->to($myEmailId);
                                $m->subject($subject);
                            });
                            
                            $mail_status_update= CronMailNotifications::where('id',$value->id)->update(['mail_status'=>'1']);
                            DB::commit();
                        } catch (\Exception $e) {
                            $mail_status_update= CronMailNotifications::where('id',$value->id)->update(['mail_status'=>'2','mail_failed_message'=>$e->getMessage()]);
                            DB::commit();
                        }
                }
            }
        }
    }
}
