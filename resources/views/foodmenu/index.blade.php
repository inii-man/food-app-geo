<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Menu
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <a href="{{ route('foodmenu.create') }}" class="mb-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition">Add New Item</a>
                   <table class="min-w-full divide-y divide-gray-200">
                       <thead>
                           <tr>
                               <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                               <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                               <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                               <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                               <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                           </tr>
                       </thead>
                       <tbody class="bg-white divide-y divide-gray-200">
                           @foreach($foodMenus as $item)
                               <tr>
                                   <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->name }}</td>
                                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->description }}</td>
                                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($item->price, 2) }}</td>
                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($item->image)
                                             <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="h-16 w-16 object-cover">
                                        @else
                                             No Image
                                        @endif
                                      </td>
                                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('foodmenu.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form action="{{ route('foodmenu.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                               </tr>
                           @endforeach
                       </tbody>
                   </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
