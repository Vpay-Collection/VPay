<style>
    ::-webkit-scrollbar {
        width: 5px;
    }

    ::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 5px;
    }


    .loading-spinner {
        --mdb-loading-spinner-top: 50%;
        --mdb-loading-spinner-left: 50%;
        --mdb-loading-spinner-transform: translate(-50%, -50%);
        --mdb-loading-spinner-color: #3b71ca;
        position: absolute;
        top: var(--mdb-loading-spinner-top);
        left: var(--mdb-loading-spinner-left);
        transform: var(--mdb-loading-spinner-transform);
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        color: var(--mdb-loading-spinner-color);
        z-index: 1056;
    }

    .position-absolute {
        position: absolute !important;
    }

    .loading-backdrop {
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: var(--mdb-datepicker-backdrop-background-color);
        z-index: var(--mdb-datepicker-zindex);
    }

    @media (prefers-color-scheme: dark) {
        body {
            background-color: #303030;
            color: #fff;
        }

        .loading-backdrop {
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-color: var(--mdb-datepicker-backdrop-background-color);
            z-index: var(--mdb-datepicker-zindex);
        }

        .position-absolute {
            position: absolute !important;
        }

        .loading-spinner {
            --mdb-loading-spinner-top: 50%;
            --mdb-loading-spinner-left: 50%;
            --mdb-loading-spinner-transform: translate(-50%, -50%);
            --mdb-loading-spinner-color: #3b71ca;
            position: absolute;
            top: var(--mdb-loading-spinner-top);
            left: var(--mdb-loading-spinner-left);
            transform: var(--mdb-loading-spinner-transform);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: var(--mdb-loading-spinner-color);
            z-index: 1056;
        }
    }


</style>

<!-- Google Fonts Roboto -->

<link rel="stylesheet" href="../../public/css/font.css?v={$__version}" media="none" onload="this.media='all'"/>
<!-- MDB ESSENTIAL -->
{if $theme == "dark"}
    <link id="theme-link" rel="preload" as="style" onload="this.rel='stylesheet'"
          href="../../public/css/mdb.dark.min.css?v={$__version}"/>
{else}
    <link id="theme-link" rel="preload" as="style" onload="this.rel='stylesheet'"
          href="../../public/css/mdb.min.css?v={$__version}"/>
{/if}
<!-- Font Awesome -->
<link rel="stylesheet" href="../../public/css/all.min.css?v={$__version}" media="none" onload="this.media='all'"/>

