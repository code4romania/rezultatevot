export default () => ({
    id: `rezultatevot-embed-${Math.floor(Date.now()).toString(36)}`,
    url: this.$wire.url,

    copy() {
        const code =
            `<iframe id="${this.id}" src="${this.url}" style="width:100%;height:100vh"></iframe>` +
            `<script src="https://cdn.jsdelivr.net/npm/@iframe-resizer/parent@5.3.2"></script>` +
            `<script>iframeResize({license:'GPLv3'},'#${this.id}');</script>`;

        this.$clipboard(code);
    },
});
