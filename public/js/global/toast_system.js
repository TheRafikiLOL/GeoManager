function loadToast(title = "", message = "") {

    const toastContainer = document.createElement('div');
    toastContainer.className = "position-fixed top-0 end-0 p-3";
    toastContainer.style.zIndex = "11";

    const toast = document.createElement('div');
    toast.className = "toast hide";
    toast.role = "alert";
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");

    toast.innerHTML = `
        <div class="toast-header">
            <strong class="me-auto">${title}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;

    toastContainer.appendChild(toast);
    document.body.appendChild(toastContainer);

    const bootstrapToast = new bootstrap.Toast(toast);
    bootstrapToast.show();
}
