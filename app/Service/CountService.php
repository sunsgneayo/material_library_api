<?php


namespace App\Service;


use App\Model\Task;
use App\Model\TaskMember;
use App\Model\TaskTodo;
use App\Model\TaskWithdraw;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\Cache\Annotation\Cacheable;

use Hyperf\Utils\ApplicationContext;

class CountService extends AbstractService
{
    /**
     * @var string
     */
    protected $startDateTime;
    /**
     * @var string
     */
    protected $endtDateTime;




    /**
     * @param string $date
     */
    private  function parseDate(string $date = "")
    {
        if ($date == "")
        {
            $date = date('Y-m-d H:i:s', time());
        }

        $carbon = Carbon::parse($date);
        $start = $carbon->startOfDay()->toDateTimeString();
        $end   = $carbon->endOfDay()->toDateTimeString();
        $this->startDateTime = $start;
        $this->endtDateTime  = $end;
    }

    /**
     * @Cacheable (prefix="MemberCount",ttl=9000)
     * @param string $date
     * @return array
     */
    public function getMemberCount(string $date = ""):array
    {
        $this->parseDate($date);
        $todayCount = TaskMember::query()->where("created_at" ,">" , $this->startDateTime)
            ->where( "created_at" ,"<" , $this->endtDateTime)->count();

        return [
            "todayCount" => $todayCount,
            "date"       => [
                "start"  => $this->startDateTime,
                "end"    => $this->endtDateTime
            ]
        ];
    }

    /**
     *@Cacheable (prefix="MemberMonthCount", ttl=9000)
     * @param string $date
     * @return array
     */
    public function getMemberMonthCount(string $date):array
    {
        $carbon = Carbon::parse($date);
        $start = $carbon->startOfMonth()->toDateTimeString();
        $end   = $carbon->endOfMonth()->toDateTimeString();
        $member  = Db::select("SELECT
        COUNT(1) AS countNumber,
        DATE_FORMAT(created_at,'%Y-%m-%d') AS dateTime
        FROM
        task_member
        where created_at > '$start' and created_at < '$end' 
        GROUP BY DATE_FORMAT(created_at,'%Y-%m-%d')");

        return $member ?? [];
    }

    /**
     * 统计当月每日新增数据
     *@Cacheable (prefix="MonthCount", ttl=9000)
     * @param string $field
     * @param string $table
     * @param string $date
     * @param string $where
     * @return array
     */
    public function getMonthCountByTable(string $field,string $table ,string $date ,string $where = ""):array
    {
        $carbon = Carbon::parse($date);
        $start = $carbon->startOfMonth()->toDateTimeString();
        $end   = $carbon->endOfMonth()->toDateTimeString();
        $count  = Db::select("SELECT
        COUNT(1) AS countNumber,
        DATE_FORMAT($field,'%Y-%m-%d') AS dateTime
        FROM $table
        WHERE $field > '$start' AND $field < '$end' AND ".$where."
        GROUP BY DATE_FORMAT($field,'%Y-%m-%d')");

        return $count ?? [];
    }
    /**
     * @Cacheable (prefix="TaskWithCount", ttl=9000)
     * @param string $date
     * @return array
     */
    public function getTaskWithdrawCount(string $date = ""): array
    {
       $this->parseDate($date);
       //当日提现成功次数
       $todaySuccessCount = TaskWithdraw::query()->where(
           [
               [
                   "updated_at" ,">=" ,$this->startDateTime
               ],
               [
                   "updated_at" ,"<=" ,$this->endtDateTime
               ],
               ["status" , "=" ,1]
           ]
       )->get();
       //当日提现成功总金额
        $todaySuccessMoneyCount = 0;
        foreach ($todaySuccessCount as $value)
        {
            $todaySuccessMoneyCount += $value->money;
        }
        //当日未完成（待审核）提现次数
       $todayFailsCount = TaskWithdraw::query()->where(
           [
               [
                   "updated_at" ,">=" ,$this->startDateTime
               ],
               [
                   "updated_at" ,"<=" ,$this->endtDateTime
               ],
               ["status","=",0]
           ]
       )->get();
        //当日未完成（待审核）总金额
        $todayFailsMoneyCount = 0;
        foreach ($todayFailsCount as $value)
        {
            $todayFailsMoneyCount += $value->money;
        }

        //当日未通过提现次数
        $todayRejectCount = TaskWithdraw::query()->where(
            [
                [
                    "updated_at" ,">=" ,$this->startDateTime
                ],
                [
                    "updated_at" ,"<=" ,$this->endtDateTime
                ],
                ["status","=",2]
            ]
        )->get();
        //当日未通过提现次数
        $todayRejectMoneyCount = 0;
        foreach ($todayRejectCount as $value)
        {
            $todayRejectMoneyCount += $value->money;
        }
        /**
         * 当日申请提现总金额
         */
        $todayMoneyCount = $todayRejectMoneyCount + $todaySuccessMoneyCount + $todayFailsMoneyCount;

       return [
           "success" => [
              "todayCount"      => $todaySuccessCount->count(),
              "todayMoneyCount" => $todaySuccessMoneyCount
           ],
           "incomplete" => [
               "todayCount"      => $todayFailsCount->count(),
               "todayMoneyCount" => $todayFailsMoneyCount
           ],
           "reject" => [
               "todayCount"      => $todayRejectCount->count(),
               "todayMoneyCount" => $todayRejectMoneyCount
           ],
           "count" => [
               "todayMoneyCount" => $todayMoneyCount
           ],
           "date"  => [
               "start"  => $this->startDateTime,
               "end"    => $this->endtDateTime
           ]
       ];
    }

    /**
     * @Cacheable (prefix="TaskTodoCount",ttl=9000)
     * @param string $date
     * @return array[]
     */
    public function getTaskTodoCount(string $date = ""):array
    {
        $this->parseDate($date);
        /**
         * 任务进行中总金额
         */
        $todayTodoIngMoneyCount = 0;
        $todayTodoIng   =   TaskTodo::query()->with('task')
            ->where(
                [
                    [
                        "created_at" ,">" ,$this->startDateTime
                    ],
                    [
                        "created_at" ,"<" ,$this->endtDateTime
                    ],
                    ["status" , "=" ,1]
                ]
            )
            ->get();
        foreach ($todayTodoIng as $value)
        {
            if ($value->task->type == 2)
            {
                $todayTodoIngMoneyCount += $value->quantity * $value->task->price;
            }else{
                $todayTodoIngMoneyCount += $value->task->price;
            }
        }

        /**
         * 任务待审核总金额
         */
        $todayAuditTodoMoneyCount = 0;
        $todayTodoAudit   =   TaskTodo::query()->with('task')
            ->where(
                [
                    [
                        "created_at" ,">" ,$this->startDateTime
                    ],
                    [
                        "created_at" ,"<" ,$this->endtDateTime
                    ],
                    ["status" , "=" ,2]
                ]
            )
            ->get();
        foreach ($todayTodoAudit as $value)
        {
            if ($value->task->type == 2)
            {
                $todayAuditTodoMoneyCount += $value->quantity * $value->task->price;
            }else{
                $todayAuditTodoMoneyCount += $value->task->price;
            }
        }

        /**
         * 任务待审核总金额
         */
        $todayAdoptTodoMoneyCount = 0;
        $todayTodoAdopt   =   TaskTodo::query()->with('task')
            ->where(
                [
                    [
                        "created_at" ,">" ,$this->startDateTime
                    ],
                    [
                        "created_at" ,"<" ,$this->endtDateTime
                    ],
                    ["status" , "=" ,3]
                ]
            )
            ->get();
        foreach ($todayTodoAdopt as $value)
        {
            if ($value->task->type == 2)
            {
                $todayAdoptTodoMoneyCount += $value->quantity * $value->task->price;
            }else{
                $todayAdoptTodoMoneyCount += $value->task->price;
            }
        }
        /**
         * 任务待审核总金额
         */
        $todayRefuseTodoMoneyCount = 0;
        $todayTodoRefuse   =   TaskTodo::query()->with('task')
            ->where(
                [
                    [
                        "created_at" ,">" ,$this->startDateTime
                    ],
                    [
                        "created_at" ,"<" ,$this->endtDateTime
                    ],
                    ["status" , "=" ,4]
                ]
            )
            ->get();
        foreach ($todayTodoRefuse as $value)
        {
            if ($value->task->type == 2)
            {
                $todayRefuseTodoMoneyCount += $value->quantity * $value->task->price;
            }else{
                $todayRefuseTodoMoneyCount += $value->task->price;
            }
        }

        return [
            "on"       => [
                "count"       => $todayTodoIng->count() ?? "",
                "moneyCount"  => $todayTodoIngMoneyCount
            ],
            "audit"    => [
                "count"       => $todayTodoAudit->count() ?? "",
                "moneyCount"  => $todayAuditTodoMoneyCount
            ],
            "adopt"    => [
                "count"       => $todayTodoAdopt->count() ?? "",
                "moneyCount"  => $todayAdoptTodoMoneyCount
            ],
            "refuse"   => [
                "count"       => $todayTodoRefuse->count() ?? "",
                "moneyCount"  => $todayRefuseTodoMoneyCount
            ],
            "date"       => [
                "start"  => $this->startDateTime,
                "end"    => $this->endtDateTime
            ]
        ];

    }

    /**
     * @Cacheable (prefix="TaskAllCount",ttl=9000)
     * @return array[]
     */
    public function getTaskAllCount(): array
    {
        //数据总记录
        $taskMemberCount   = TaskMember::query()->count();
        $taskCount         = Task::query()->count();
        $taskWithdrawCount = TaskWithdraw::query()->count();
        $taskTodo          = TaskTodo::query()->count();
        /**
         * 会员禁用总数
         */
        $taskMenberDisable = TaskMember::query()->where("status","=",2)->count();
        /**
         * 提现成功总金额
         */
        $taskWithdrawMoneySuccessCount = 0;
        $taskSuccess   =    TaskWithdraw::query()->where("status","=",1)->get();
        foreach ($taskSuccess as $value)
        {
            $taskWithdrawMoneySuccessCount += $value->money;
        }
        /**
         * 提现未完成总金额
         */
        $taskWithdrawMoneyIncompleteCount = 0;
        $taskIncomplete   =    TaskWithdraw::query()->where("status","=",0)->get();
        foreach ($taskIncomplete as $value)
        {
            $taskWithdrawMoneyIncompleteCount += $value->money;
        }
        /**
         * 提现未通过总金额
         */
        $taskWithdrawMoneyRejectCount = 0;
        $taskReject  =    TaskWithdraw::query()->where("status","=",2)->get();
        foreach ($taskReject as $value)
        {
            $taskWithdrawMoneyRejectCount += $value->money;
        }

        /**
         * 任务进行中总金额
         */

        $taskTodoIngMoneyCount = 0;
        $taskTodoIng   =   TaskTodo::query()->with('task')->where("status","=",1)->get();
        foreach ($taskTodoIng as $value)
        {
            if ($value->task->type == 2)
            {
                $taskTodoIngMoneyCount += $value->quantity * $value->task->price;
            }else{
                $taskTodoIngMoneyCount += $value->task->price;
            }
        }
        /**
         * 任务待审核总金额
         */

        $taskTodoAuditMoneyCount = 0;
        $taskTodoAudit   =   TaskTodo::query()->with('task')->where("status","=",2)->get();
        foreach ($taskTodoAudit as $value)
        {
            if ($value->task->type == 2)
            {
                $taskTodoAuditMoneyCount += $value->quantity * $value->task->price;
            }else{
                $taskTodoAuditMoneyCount += $value->task->price;
            }
        }

        /**
         * 任务已通过总金额
         */

        $taskTodoAdoptMoneyCount = 0;
        $taskTodoAdopt   =   TaskTodo::query()->with('task')->where("status","=",3)->get();
        foreach ($taskTodoAdopt as $value)
        {
            if ($value->task->type == 2)
            {
                $taskTodoAdoptMoneyCount += $value->quantity * $value->task->price;
            }else{
                $taskTodoAdoptMoneyCount += $value->task->price;
            }
        }
        /**
         * 任务未通过总金额
         */

        $taskTodoRefuseMoneyCount = 0;
        $taskTodoRefuse   =   TaskTodo::query()->with('task')->where("status","=",4)->get();
        foreach ($taskTodoRefuse as $value)
        {
            if ($value->task->type == 2)
            {
                $taskTodoRefuseMoneyCount += $value->quantity * $value->task->price;
            }else{
                $taskTodoRefuseMoneyCount += $value->task->price;
            }
        }

        return [
            "taskTodo" => [
                "count" => $taskTodo,
                "on"       => [
                    "count"       => $taskTodoIng->count() ?? "",
                    "moneyCount"  => $taskTodoIngMoneyCount
                ],
                "audit"    => [
                    "count"       => $taskTodoAudit->count() ?? "",
                    "moneyCount"  => $taskTodoAuditMoneyCount
                ],
                "adopt"    => [
                    "count"       => $taskTodoAdopt->count() ?? "",
                    "moneyCount"  => $taskTodoAdoptMoneyCount
                ],
                "refuse"   => [
                    "count"       => $taskTodoRefuse->count() ?? "",
                    "moneyCount"  => $taskTodoRefuseMoneyCount
                ],
            ],
            "member"  =>[
                "count"        => $taskMemberCount,
                "disableCount" => $taskMenberDisable
            ],
            "task"    =>[
                "count" => $taskCount,
            ],
            "taskWithdraw"   => [
                "count"   => $taskWithdrawCount,
                "success" => [
                    "moneyCount" => $taskWithdrawMoneySuccessCount,
                    "count"      => $taskSuccess->count()
                ],
                "incomplete" => [
                    "moneyCount" => $taskWithdrawMoneyIncompleteCount,
                    "count"      => $taskIncomplete->count()
                ],
                "reject" => [
                    "moneyCount" => $taskWithdrawMoneyRejectCount,
                    "count"      => $taskReject->count()
                ]
            ]

        ];
    }


    /**
     * @Cacheable (prefix="UserTaskRankingList",ttl=9000)
     * @return array
     */
    public function getUserTaskRankingList():array
    {
        $rank = TaskTodo::query()->with('member')->where('status','=','3')
            ->groupBy(['member_id'])
            ->havingRaw('count(member_id) >= 2')
            ->selectRaw('member_id,count(id) as count')
            ->orderByRaw('count(id) DESC')
            ->get();

        $list = [];
        if ($rank->isNotEmpty())
        {
            $rank = $rank->toArray();
            for ($i = 0 ; $i < 10 ; $i++)
            {
                $list[] = [
                    "rank"   => $i + 1,
                    "count"  => $rank[$i]['count'],
                    "member"  => $rank[$i]['member'],
                ];
            }
        }
        return $list;
    }
}