export function loadingStateComponent() {
    return {
        loading: false,
        
        startLoading() {
            this.loading = true;
        },
        
        stopLoading() {
            this.loading = false;
        }
    };
}