export default () => ({
    id: `rezultatevot-embed-${Math.floor(Date.now()).toString(36)}`,
    url: null,
    isSuccesful: false,

    init() {
        this.url = this.$wire.url;
    },

    copy() {
        const code =
            `<iframe id="${this.id}" src="${this.url}" style="width:100%;height:100vh;border:0;"></iframe>` +
            `<script src="https://cdn.jsdelivr.net/npm/@iframe-resizer/parent@5.3.2"></script>` +
            `<script>iframeResize({license:'GPLv3'},'#${this.id}');</script>`;

        this.$clipboard(code);

        this.isSuccesful = true;

        setTimeout(() => {
            this.isSuccesful = false;
        }, 5000);
    },
});
