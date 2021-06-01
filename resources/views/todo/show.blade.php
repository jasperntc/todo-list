<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Show Task
        </h2>
    </x-slot>

    <div>
        <div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8">
                <a href="{{ route('todo.index') }}" class="bg-gray-200 hover:bg-gray-300 text-black font-bold py-2 px-4 rounded">Back to list</a>
            </div>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 w-full">
                                <tr class="border-b">
                                    <th scope="col" width="200" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                        {{ $todo->id }}
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <th scope="col" width="200" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                        {{ $todo->description }}
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <th scope="col" width="200" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Published by
                                    </th>
                                    <?php
                                        $user = auth()->user();
                                        if($user->id == $todo->owner){
                                            $name = "You";
                                        }
                                        else{
                                            $query_result = DB::select('select name from users where id = '.$todo->owner);
                                            $name = collect($query_result)->pluck('name');
                                            $name = preg_replace("/[^A-Za-z0-9\-]/", '', $name);
                                            $name = preg_replace("/-+/", ' ', $name);
                                        }
                                    ?>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                        {{ $name }}
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <th scope="col" width="200" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Checked by
                                    </th>
                                    <?php
                                        $user = auth()->user();
                                        $query_result = DB::select('select user_id from checklists where todo_id = '.$todo->id);
                                        $checked_userid = collect($query_result)->pluck('user_id')->toArray();
                                        $count = 0;
                                        $string = "Nobody checked this task yet...";
                                        if(in_array($user->id, $checked_userid)){
                                            $string = "You";
                                            $count++;
                                            if(($key = array_search($user->id, $checked_userid)) !== false) {
                                                unset($checked_userid[$key]);
                                            }
                                        }
                                        foreach($checked_userid as $key){
                                            $query_result = DB::select('select name from users where id = '.$key);
                                            $name = collect($query_result)->pluck('name');
                                            $name = preg_replace("/[^A-Za-z0-9\-]/", '', $name);
                                            $name = preg_replace("/-+/", ' ', $name);
                                            if($count == 0){
                                                $string = $name;
                                                $count++;
                                            }
                                            elseif($count >= 1 && $count < 3){
                                                $string .= ", ".$name;
                                                $count++;
                                            }
                                            else{
                                                $count++;
                                            }
                                        }
                                        if($count >= 4){
                                            $string .= ", and ".($count-3)." more...";
                                        }
                                    ?>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                        {{ $string }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                $query_result = DB::select('select * from checklists where todo_id = '.$todo->id);
                $checked_userid = collect($query_result)->pluck('user_id')->toArray();

                if(in_array($user->id, $checked_userid)){
                    $query_result = DB::select('select id from checklists where todo_id = '.$todo->id.' and user_id = '.$user->id);
                    $checklist_id = collect($query_result)->pluck('id');
                    $checklist_id = preg_replace("/[^A-Za-z0-9\-]/", '', $checklist_id);
                    ?>
                    <form action="{{ route('checklist.destroy', $checklist_id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
                            <input type="submit" class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150" value="Uncheck this task">
                        </div>
                    </form>
                    <!-- <form method="delete" action="{{ route('checklist.destroy', $checklist_id) }}" onsubmit="return confirm('Are you sure?');">
                        <div class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
                            <button class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150">
                            Uncheck this task
                            </button>
                        </div>
                    </form> -->
                    <?php
                }
                else{
                    ?>
                    <form method="post" action="{{ route('checklist.store') }}">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="todo_id" id="todo_id" value="{{ $todo->id }}">
                        <div class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
                            <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                            Check this task
                            </button>
                        </div>
                    </form>
                    <?php
                }
            ?>
        </div>
    </div>
</x-app-layout>