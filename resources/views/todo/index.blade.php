<?php
use Illuminate\Support\Facades\DB;
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Todo-List') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8">
                <a href="{{ route('todo.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Add Task</a>
            </div>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 w-full">
                                <thead>
                                <tr>
                                    <th scope="col" width="50" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th scope="col" width="200" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Published by
                                    </th>
                                    <th scope="col" width="200" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($todo as $key)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $key->id }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $key->description }}
                                        </td>

                                        <?php
                                        $user = auth()->user();
                                        if($user->id == $key->owner){
                                            $name = "You";
                                        }
                                        else{
                                            $query_result = DB::select('select name from users where id = '.$key->owner);
                                            $name = collect($query_result)->pluck('name');
                                            $name = preg_replace("/[^A-Za-z0-9\-]/", '', $name);
                                            $name = preg_replace("/-+/", ' ', $name);
                                        }
                                        ?>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $name }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('todo.show', $key->id) }}" class="text-blue-600 hover:text-blue-900 mb-2 mr-2">View</a>
                                            <?php
                                            $user = auth()->user();
                                            $query_result = DB::select('select role_id from role_user where user_id = '.$user->id);
                                            $userRole = collect($query_result)->pluck('role_id');
                                            $userRole = preg_replace("/[^A-Za-z0-9\-]/", '', $userRole);
                                            if($user->id == $key->owner || $userRole == 1){
                                            ?>
                                                <a href="{{ route('todo.edit', $key->id) }}" class="text-indigo-600 hover:text-indigo-900 mb-2 mr-2">Edit</a>
                                                <form class="inline-block" action="{{ route('todo.destroy', $key->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="submit" class="text-red-600 hover:text-red-900 mb-2 mr-2" value="Delete">
                                                </form>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
