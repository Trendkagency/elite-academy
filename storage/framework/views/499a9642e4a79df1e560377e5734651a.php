<script>
    (function() {
        function isLogoutElement(el) {
            if (!el) return true;
            const form = el.closest("form");
            if (form) {
                const action = (form.getAttribute("action") || "").toLowerCase();
                if (action.includes("logout") || action.includes("sign-out") || action.includes("signout")) return true;
            }
            const txt = (el.textContent || "").trim().toLowerCase();
            const href = (el.getAttribute("href") || "").toLowerCase();
            const wireClick = (el.getAttribute("wire:click") || "").toLowerCase();
            if (txt.includes("logout") || txt.includes("sign out") || txt.includes("تسجيل الخروج") || txt.includes("خروج")) return true;
            if (href.includes("logout") || wireClick.includes("logout")) return true;
            return false;
        }

        document.addEventListener("keydown", function (e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === "s" || e.key === "S" || e.code === "KeyS")) {
                e.preventDefault();
                e.stopPropagation();

                let saveBtn = null;

                // 1. Check active modal first
                const activeModal = document.querySelector(".fi-modal:not(.hidden), [role='dialog']:not(.hidden), .fi-modal-window");
                if (activeModal) {
                    const modalButtons = Array.from(activeModal.querySelectorAll("button[type='submit'], [wire\\:click*='save'], [wire\\:click*='create'], [wire\\:click*='submit'], .fi-btn-color-primary"));
                    saveBtn = modalButtons.find(b => !b.disabled && !isLogoutElement(b));
                }

                // 2. Check main form / page submit actions (explicitly filtering out any logout form/button)
                if (!saveBtn) {
                    const pageButtons = Array.from(document.querySelectorAll(
                        ".fi-form-actions button[type='submit'], " +
                        ".fi-form-actions button.fi-btn-color-primary, " +
                        ".fi-page-header-actions button.fi-btn-color-primary, " +
                        "form:not([action*='logout']) button[type='submit'], " +
                        "button[wire\\:click*='save'], " +
                        "button[wire\\:click*='create'], " +
                        "button[wire\\:click*='update'], " +
                        "button[wire\\:click*='submit']"
                    ));
                    saveBtn = pageButtons.find(b => !b.disabled && !isLogoutElement(b));
                }

                // 3. Fallback: Search buttons by text content (excluding logout buttons)
                if (!saveBtn) {
                    const allButtons = Array.from(document.querySelectorAll("button:not([disabled])"));
                    saveBtn = allButtons.find(b => {
                        if (isLogoutElement(b)) return false;
                        const txt = (b.textContent || "").trim().toLowerCase();
                        return txt.includes("save") || txt.includes("حفظ") || txt.includes("update") || txt.includes("تحديث") || txt.includes("create") || txt.includes("إنشاء");
                    });
                }

                if (saveBtn) {
                    // Show subtle visual shortcut feedback indicator
                    const isAr = document.documentElement.lang === "ar" || document.dir === "rtl";
                    const toast = document.createElement("div");
                    toast.className = "fixed top-4 right-4 z-[99999] bg-slate-900 text-white font-mono text-xs px-4 py-2.5 rounded-xl shadow-2xl flex items-center gap-2 border border-slate-700 transition-all duration-300 transform translate-y-0 opacity-100";
                    toast.innerHTML = "<span>💾</span> <span>" + (isAr ? "جاري الحفظ... (Ctrl + S)" : "Saving Changes... (Ctrl + S)") + "</span>";
                    document.body.appendChild(toast);

                    setTimeout(function() {
                        toast.style.opacity = "0";
                        toast.style.transform = "translateY(-8px)";
                        setTimeout(function() { toast.remove(); }, 300);
                    }, 1200);

                    saveBtn.click();
                }
            }
        }, true);
    })();
</script>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/filament/hooks/ctrl-s-shortcut.blade.php ENDPATH**/ ?>