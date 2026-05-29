@extends('admin.layouts.admin_layout')

@section('title', 'Emergency Broadcast - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Emergency Broadcast</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Blood Donation Hub</h2>
        <p class="text-slate-500 text-sm mt-1">Manage blood stocks, events, appointments, and emergencies.</p>
    </div>
</div>

@include('admin.donor.tabs')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Broadcast Form -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-red-200 dark:border-red-900 shadow-sm overflow-hidden border-t-4 border-t-red-500">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center">
                    <span class="material-icons-round text-red-500 mr-2">campaign</span>
                    New Broadcast
                </h3>
            </div>
            <form action="{{ route('admin.blood-emergency.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Hospital Name</label>
                        <input type="text" name="hospital_name" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. RS Medika Utama">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Blood Type</label>
                            <select name="blood_type_needed" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Bags Needed</label>
                            <input type="number" name="bags_needed" min="1" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Urgency Level</label>
                        <select name="urgency_level" required class="w-full px-4 py-2 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg text-sm font-bold focus:ring-2 focus:ring-red-500">
                            <option value="critical">CRITICAL (Within 2 Hours)</option>
                            <option value="high">HIGH (Today)</option>
                            <option value="medium">MEDIUM (Tomorrow)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Contact Person</label>
                        <input type="text" name="contact_person" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Contact Phone</label>
                        <input type="text" name="contact_phone" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0812...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Additional Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                    </div>
                </div>
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <button type="submit" onclick="return confirm('Are you sure you want to broadcast this to all matching donors?')" class="w-full py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all text-sm font-bold flex items-center justify-center space-x-2">
                        <span class="material-icons-round text-lg">notifications_active</span>
                        <span>Send Broadcast Now</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Active Requests -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Broadcast History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4">Request Details</th>
                            <th class="px-6 py-4">Blood Needed</th>
                            <th class="px-6 py-4">Urgency</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($requests as $request)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-white text-sm">{{ $request->hospital_name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $request->contact_person }} ({{ $request->contact_phone }})</div>
                                <div class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <span class="font-black text-red-600 text-lg">{{ $request->blood_type_needed }}</span>
                                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ $request->bags_needed }} Bags</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($request->urgency_level == 'critical')
                                    <span class="px-2.5 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full text-xs font-bold uppercase">{{ $request->urgency_level }}</span>
                                @elseif($request->urgency_level == 'high')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 rounded-full text-xs font-bold uppercase">{{ $request->urgency_level }}</span>
                                @else
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full text-xs font-bold uppercase">{{ $request->urgency_level }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($request->is_fulfilled)
                                    <span class="text-emerald-500 font-bold text-sm"><span class="material-icons-round text-sm align-middle mr-1">check_circle</span>Fulfilled</span>
                                @else
                                    <span class="text-amber-500 font-bold text-sm"><span class="material-icons-round text-sm align-middle mr-1">hourglass_empty</span>Waiting</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
