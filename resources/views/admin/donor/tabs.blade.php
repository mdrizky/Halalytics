<div class="mb-6 border-b border-slate-200 dark:border-slate-800">
    <nav class="-mb-px flex space-x-6">
        <a href="{{ route('admin.blood-stocks.index') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ request()->routeIs('admin.blood-stocks*') ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300 dark:hover:border-slate-600' }}">
            <div class="flex items-center space-x-2">
                <span class="material-icons-round text-lg">bloodtype</span>
                <span>Blood Stocks</span>
            </div>
        </a>
        <a href="{{ route('admin.blood-events.index') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ request()->routeIs('admin.blood-events*') ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300 dark:hover:border-slate-600' }}">
            <div class="flex items-center space-x-2">
                <span class="material-icons-round text-lg">event</span>
                <span>Donor Events</span>
            </div>
        </a>
        <a href="{{ route('admin.blood-appointments.index') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ request()->routeIs('admin.blood-appointments*') ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300 dark:hover:border-slate-600' }}">
            <div class="flex items-center space-x-2">
                <span class="material-icons-round text-lg">groups</span>
                <span>Appointments</span>
            </div>
        </a>
        <a href="{{ route('admin.blood-emergency.index') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ request()->routeIs('admin.blood-emergency*') ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300 dark:hover:border-slate-600' }}">
            <div class="flex items-center space-x-2">
                <span class="material-icons-round text-lg">emergency</span>
                <span>Emergency Calls</span>
            </div>
        </a>
    </nav>
</div>
