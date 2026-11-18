import { inject, provide } from 'vue';

export const SIDEBAR_CONTEXT_KEY = Symbol('koneksa-sidebar');

export function provideSidebarContext(context) {
    provide(SIDEBAR_CONTEXT_KEY, context);
}

export function useSidebar() {
    const context = inject(SIDEBAR_CONTEXT_KEY, null);
    if (!context) {
        throw new Error('useSidebar must be used within a SidebarProvider.');
    }

    return context;
}
