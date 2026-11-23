export function confirmModalComponent() {
    return {
        open: false,
        
        openModal() {
            this.open = true;
        },
        
        closeModal() {
            this.open = false;
        },
        
        submitForm() {
            this.$refs.form.submit();
        }
    };
}