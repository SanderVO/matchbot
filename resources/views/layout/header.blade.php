<nav class="relative w-full flex bg-white p-4 drop-shadow-lg">
    <div class="flex justify-between">
        <button @click="sidebarVisible = !sidebarVisible">
            <span x-show="!sidebarVisible">Open</span>
            <span x-show="sidebarVisible">Close</span>
        </button>
    </div>
</nav>