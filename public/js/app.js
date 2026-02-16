$(document).ready(function () {
    const $chatContainer = $("#chat-container");
    const $messagesWrapper = $("#messages-wrapper");
    const $messageInput = $("#message-input");
    const $analyticsForm = $("#analytics-form");
    const $historyList = $("#history-list");
    const $dbSelector = $("#db-selector");
    const $sendBtn = $("#send-btn");
    const csrfToken = $('meta[name="csrf-token"]').attr("content");
    let currentChatId = new URLSearchParams(window.location.search).get(
        "chat_id",
    );
    let hasDbConfigs = false;

    // Initialize
    if (currentChatId) {
        loadChat(currentChatId);
    }
    loadHistory();
    loadDbConfigs();
    restoreSelectedDatabase();

    // Event Handlers
    $analyticsForm.on("submit", function (e) {
        e.preventDefault();
        handleSubmit();
    });

    $dbSelector.on("change", function () {
        saveSelectedDatabase();
        updateSendState();
    });

    $messageInput.on("input", function () {
        this.style.height = "";
        this.style.height = this.scrollHeight + "px";
        updateSendState();
    });

    $messageInput.on("keydown", function (e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            $analyticsForm.submit();
        }
    });

    $("#toggle-sidebar-mobile").on("click", toggleSidebar);
    $("#sidebar-overlay").on("click", toggleSidebar);

    // Functions
    function toggleSidebar() {
        $("#sidebar").toggleClass("-translate-x-full");
        $("#sidebar-overlay").toggleClass("hidden");
    }

    window.sendPromptAndHide = function (text) {
        $("#welcome-section").remove();
        $messageInput.val(text);
        updateSendState();
        handleSubmit();
    };

    function loadHistory() {
        $.ajax({
            url: "/analytics/history",
            type: "GET",
            headers: { Accept: "application/json" },
            success: function (histories) {
                $historyList.empty();

                if (histories.length === 0) {
                    $historyList.html(
                        '<div class="text-center text-gray-500 text-xs py-10">No recent analyses</div>',
                    );
                    return;
                }

                histories.forEach((h) => {
                    const isActive = currentChatId == h.id;
                    const $item = $("<div>").addClass(
                        `sidebar-item group flex items-center gap-3 px-4 py-3 rounded-2xl cursor-pointer ${isActive ? "active shadow-lg" : "text-gray-400"} relative`,
                    );

                    const $titleBtn = $("<button>")
                        .addClass(
                            "flex items-center gap-3 flex-1 min-w-0 text-left",
                        )
                        .on("click", () => loadChat(h.id));
                    $titleBtn.html(`
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        <span class="text-sm font-medium truncate chat-title-text" data-id="${h.id}">${h.title || "Untitled analysis"}</span>
                    `);

                    const $menuBtn = $("<button>").addClass(
                        "opacity-0 group-hover:opacity-100 p-1 hover:bg-white/10 rounded transition-all",
                    );
                    $menuBtn.html(
                        '<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>',
                    );
                    $menuBtn.on("click", (e) => {
                        e.stopPropagation();
                        showMenu(h.id, $(e.currentTarget));
                    });

                    $item.append($titleBtn).append($menuBtn);
                    $historyList.append($item);
                });
            },
            error: function(e) {
                console.error(e);
            },
        });
    }

    function showMenu(chatId, $button) {
        $(".context-menu").remove();
        const $menu = $("<div>").addClass("context-menu absolute right-2 top-full mt-1 bg-[#2a2b2d] border border-white/10 rounded-xl shadow-2xl py-2 z-50 w-32");
        $menu.html(`
            <button class="rename-chat-btn w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-white/5">Rename</button>
            <button class="delete-chat-btn w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-white/5">Delete</button>
        `);

        $menu.find(".rename-chat-btn").on("click", () => renameChat(chatId));
        $menu.find(".delete-chat-btn").on("click", () => deleteChat(chatId));

        $button.parent().append($menu);

        setTimeout(() => {
            $(document).one("click", function (e) {
                if (!$menu.is(e.target) && $menu.has(e.target).length === 0) {
                    $menu.remove();
                }
            });
        }, 0);
    }

    function renameChat(id) {
        const $textEl = $(`.chat-title-text[data-id="${id}"]`);
        const oldTitle = $textEl.text();
        const $input = $("<input>")
            .attr("type", "text")
            .val(oldTitle)
            .addClass(
                "bg-white/10 text-white text-sm rounded px-2 py-1 w-full outline-none ring-1 ring-indigo-500",
            );

        $textEl.addClass("hidden");
        $textEl.parent().append($input);
        $input.focus();
        $input.on("click mousedown", (e) => e.stopPropagation());

        const finish = (save) => {
            const newTitle = $input.val().trim();
            $input.remove();
            $textEl.removeClass("hidden");
            if (save && newTitle && newTitle !== oldTitle) {
                $.ajax({
                    url: `/analytics/rename/${id}`,
                    type: "PUT",
                    data: JSON.stringify({ title: newTitle }),
                    contentType: "application/json",
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    success: function () {
                        $textEl.text(newTitle);
                        loadHistory();
                    },
                    error: function (e) {
                        console.error(e);
                    },
                });
            }
        };

        $input.on("keydown", (e) => {
            e.stopPropagation();
            if (e.key === "Enter") finish(true);
            if (e.key === "Escape") finish(false);
        });
        $input.on("blur", () => finish(true));
    }

    function deleteChat(id) {
        Swal.fire({
            title: "Delete analysis?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4f46e5",
            cancelButtonColor: "#ef4444",
            confirmButtonText: "Yes, delete it!",
            background: "#ffffff",
            customClass: {
                popup: "rounded-3xl",
                confirmButton: "rounded-xl px-6 py-3",
                cancelButton: "rounded-xl px-6 py-3",
            },
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/analytics/delete/${id}`,
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                    },
                    success: function () {
                        if (currentChatId == id) {
                            currentChatId = null;
                            window.history.pushState({}, "", "/analytics");
                            location.reload();
                        } else {
                            loadHistory();
                        }
                    },
                    error: function (e) {
                        console.error(e);
                        showToast("Failed to delete chat", "error");
                    },
                });
            }
        });
    }

    window.startNewChat = function () {
        currentChatId = null;
        window.history.pushState({}, "", "/analytics");
        location.reload();
    };

    function loadChat(id) {
        // If we're not on the analytics page, navigate there first
        if (!window.location.pathname.includes("/analytics")) {
            window.location.href = `/analytics?chat_id=${id}`;
            return;
        }

        currentChatId = id;
        const url = new URL(window.location);
        url.searchParams.set("chat_id", id);
        window.history.pushState({}, "", url);

        loadHistory();
        if ($(window).width() < 768) toggleSidebar();

        $("#welcome-section").remove();
        $messagesWrapper.html(
            '<div class="py-20 text-center text-gray-400 animate-pulse">Loading mission data...</div>',
        );

        $.ajax({
            url: `/analytics/history/${id}`,
            type: "GET",
            headers: { Accept: "application/json" },
            success: function (messages) {
                $messagesWrapper.empty();
                messages
                    .filter((msg) => msg.content && msg.content.trim() !== "")
                    .forEach((msg) => {
                        appendMessage(
                            msg.role === "user" ? "user" : "ai",
                            msg.content,
                            false,
                        );
                    });
                scrollToBottom();
            },
            error: function () {
                showToast("Failed to load mission data");
            },
        });
    }

    function loadDbConfigs() {
        $.ajax({
            url: "/api/db-config/list",
            type: "GET",
            headers: { Accept: "application/json" },
            success: function (data) {
                const hasConfigs =
                    data.databases.length > 0 || data.sqlFiles.length > 0;
                hasDbConfigs = hasConfigs;

                $dbSelector.html("");
                if (hasConfigs) {
                    $dbSelector.append(
                        '<option value="" selected>Select database...</option>',
                    );
                } else {
                    $dbSelector.append(
                        '<option value="" selected>Please add your database first</option>',
                    );
                }

                data.databases.forEach((db) => {
                    // $dbSelector.append($('<option>').val(`db:${db.id}`).text(`DB: ${db.name}`));
                    $dbSelector.append(
                        $("<option>").val(`db:${db.id}`).text(`${db.name}`),
                    );
                });

                data.sqlFiles.forEach((sql) => {
                    // $dbSelector.append($('<option>').val(`sql:${sql.id}`).text(`SQL: ${sql.name}`));
                    $dbSelector.append(
                        $("<option>").val(`sql:${sql.id}`).text(`${sql.name}`),
                    );
                });

                // Restore previously selected database after options are loaded
                restoreSelectedDatabase();
                updateSendState();
            },
            error: function (e) {
                console.error("Failed to load configs", e);
            },
        });
    }

    function saveSelectedDatabase() {
        const selectedValue = $dbSelector.val();
        if (selectedValue) {
            localStorage.setItem("selectedDatabase", selectedValue);
        } else {
            localStorage.removeItem("selectedDatabase");
        }
    }

    function restoreSelectedDatabase() {
        const savedDatabase = localStorage.getItem("selectedDatabase");
        if (savedDatabase) {
            // Check if the option exists before setting it
            if (
                $dbSelector.find(`option[value="${savedDatabase}"]`).length > 0
            ) {
                $dbSelector.val(savedDatabase);
            }
        }
    }

    function updateSendState() {
        const message = $messageInput.val().trim();
        const selectedDb = $dbSelector.val();
        const canSend = message.length > 0 && hasDbConfigs && !!selectedDb;
        $sendBtn.prop("disabled", !canSend);
        if (!hasDbConfigs) {
            $messageInput
                .prop("disabled", true)
                .attr("placeholder", "Please add your database first...");
        } else {
            $messageInput
                .prop("disabled", false)
                .attr("placeholder", "Enter an analysis prompt...");
        }
    }

    function handleSubmit() {
        const message = $messageInput.val().trim();
        if (!message) return;

        if (!hasDbConfigs) {
            showToast("Please add your database first");
            return;
        }

        const selectedDb = $dbSelector.val();
        if (!selectedDb) {
            showToast("Please select a database first");
            return;
        }

        $("#welcome-section").remove();
        appendMessage("user", message);
        $messageInput.val("").css("height", "auto");

        const loadingId = showLoading();

        $.ajax({
            url: "/analytics/chat",
            type: "POST",
            data: JSON.stringify({
                message,
                chat_id: currentChatId,
                db_config: selectedDb,
            }),
            contentType: "application/json",
            headers: { "X-CSRF-TOKEN": csrfToken, Accept: "application/json" },
            success: function (data) {
                removeLoading(loadingId);
                appendMessage("ai", data.content);
                if (data.chat_id && !currentChatId) {
                    currentChatId = data.chat_id;
                    const url = new URL(window.location);
                    url.searchParams.set("chat_id", data.chat_id);
                    window.history.pushState({}, "", url);
                    loadHistory();
                }
            },
            error: function (xhr) {
                removeLoading(loadingId);
                const data = xhr.responseJSON;
                appendMessage(
                    "ai",
                    `**Error:** ${data?.message || "Mission failure."}`,
                );
            },
        });
    }

    function appendMessage(role, content, animate = true) {
        const isAi = role === "ai";
        const $div = $("<div>").addClass(
            `flex group ${!isAi ? "justify-end" : "justify-start"} ${animate ? "opacity-0 translate-y-4 transition-all duration-300" : ""}`,
        );

        const bubbleClass = !isAi
            ? "chat-bubble-user px-6 py-4 max-w-[85%]"
            : "chat-bubble-ai px-6 py-5 max-w-[95%]";
        const proseClass = !isAi
            ? "text-gray-800"
            : "prose prose-indigo prose-sm md:prose-base text-gray-800";

        $div.html(`
            <div class="${bubbleClass}">
                <div class="${proseClass}">
                    ${isAi ? marked.parse(content) : content.replace(/\n/g, "<br>")}
                </div>
            </div>
        `);

        $messagesWrapper.append($div);
        if (animate) {
            $div.get(0).offsetWidth;
            $div.removeClass("opacity-0 translate-y-4");
        }
        scrollToBottom();
    }

    function showLoading() {
        const id = "loading-" + Date.now();
        const $div = $("<div>").attr("id", id).addClass("flex justify-start");
        $div.html(
            `<div class="chat-bubble-ai px-8 py-5 flex items-center h-16"><div class="dot-flashing"></div></div>`,
        );
        $messagesWrapper.append($div);
        scrollToBottom();
        return id;
    }

    function removeLoading(id) {
        $(`#${id}`).remove();
    }

    function scrollToBottom() {
        $chatContainer.stop().animate(
            {
                scrollTop: $chatContainer[0].scrollHeight,
            },
            500,
        );
    }

    window.showToast = function (message, type = "error") {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        });

        Toast.fire({
            icon: type,
            title: message,
            background: "#ffffff",
            customClass: {
                popup: "rounded-2xl shadow-xl border border-gray-100",
            },
        });
    };
    updateSendState();
});
