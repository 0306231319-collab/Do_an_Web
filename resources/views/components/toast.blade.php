<div x-data="{ show: false, msg: '' }"
     @toast.window="msg = $event.detail.message; show = true; setTimeout(() => show = false, 3000)"
     x-show="show" x-transition x-cloak
     class="fixed top-4 right-4 z-50 bg-green-600 text-white px-4 py-2 rounded shadow">
    <span x-text="msg"></span>
</div>