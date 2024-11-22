export default () => ({
    id: `rezultatevot-embed-${Math.floor(Date.now()).toString(36)}`,
    url: null,

    init() {
        this.url = this.$wire.url;


    },

    copy() {

        let searchParams = new URLSearchParams();
        for (const key in this.$wire.queryParams) {
            searchParams.append(key, this.$wire.queryParams[key]);
        }
        let url = new URL(this.url);
        url.search = searchParams.toString();
        const code =
            `<iframe id="${this.id}" src="${url.toString()}" style="width:100%;height:100vh"></iframe>` +
            `<script src="https://cdn.jsdelivr.net/npm/@iframe-resizer/parent@5.3.2"></script>` +
            `<script>iframeResize({license:'GPLv3'},'#${this.id}');</script>`;

        this.$clipboard(code);
    },
});
