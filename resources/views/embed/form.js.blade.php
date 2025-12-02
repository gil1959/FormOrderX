(function() {
    const token = "{{ $form->embed_token }}";

    // Cek dulu apakah sudah ada container
    let container = document.getElementById("form-embed-" + token);

    if (!container) {
        container = document.createElement("div");
        container.id = "form-embed-" + token;
        container.style.maxWidth = "480px";     // form lebar 480px
        container.style.margin = "0 auto";      // auto center
        container.style.padding = "20px 10px";
        container.style.boxSizing = "border-box";

        // Script akan menempel tepat sebelum </script>
        document.currentScript.insertAdjacentElement("beforebegin", container);
    }

    // Inject HTML form
    container.innerHTML = `
        {!! $html !!}
    `;
})();
