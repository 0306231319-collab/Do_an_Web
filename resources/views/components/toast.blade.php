<div
    x-data="{ show: false, msg: '', timer: null }"
    @toast.window="
        msg = $event.detail?.message || 'Đã hoàn tất';
        show = true;
        clearTimeout(timer);
        timer = setTimeout(() => show = false, 3000);
    "
    x-show="show"
    x-transition
    x-cloak
    role="status"
    aria-live="polite"
    class="fixed right-4 top-4 z-[60] max-w-sm rounded-lg bg-emerald-700 px-4 py-3 text-white shadow-lg"
>
    <span x-text="msg"></span>
</div>
