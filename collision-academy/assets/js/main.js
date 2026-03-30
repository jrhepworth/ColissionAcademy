(function () {
	"use strict";

	const config = window.caConfig || {};
	const i18n = config.i18n || {};
	const header = document.getElementById("ca-header");
	const navToggle = document.getElementById("ca-nav-toggle");
	const searchToggle = document.getElementById("ca-search-toggle");
	const searchWrap = document.querySelector(".ca-search-wrap");
	const searchForm = document.getElementById("ca-search-form");
	const searchInput = document.getElementById("ca-search-input");
	const progressBar = document.getElementById("ca-progress-bar");
	const mobileBreakpoint = window.matchMedia("(max-width: 900px)");

	function setMessage(element, text, type) {
		if (!element) {
			return;
		}

		element.textContent = text || "";
		element.classList.remove("is-success", "is-error");

		if (type) {
			element.classList.add(type === "success" ? "is-success" : "is-error");
		}
	}

	function clearFieldErrors(form) {
		form.querySelectorAll(".ca-form-field__error").forEach((error) => {
			error.textContent = "";
		});
	}

	function setFieldError(form, field, message) {
		if (!field) {
			return;
		}

		const target = form.querySelector(`#ca-contact-${field}-error`);
		if (!target) {
			return;
		}

		target.textContent = message;
	}

	function setSubmittingState(form, isSubmitting) {
		const submitButton = form.querySelector('[type="submit"]');
		if (!submitButton) {
			return;
		}

		if (!submitButton.dataset.originalText) {
			submitButton.dataset.originalText = submitButton.textContent.trim();
		}

		submitButton.disabled = isSubmitting;
		submitButton.textContent = isSubmitting
			? submitButton.dataset.loading || i18n.sending || "Sending..."
			: submitButton.dataset.original || submitButton.dataset.originalText;
	}

	async function sendAjaxForm(form, messageTarget, isContactForm) {
		if (!config.ajaxUrl) {
			setMessage(messageTarget, i18n.error || "Something went wrong. Please try again.", "error");
			return;
		}

		clearFieldErrors(form);
		setMessage(messageTarget, "", null);
		setSubmittingState(form, true);

		try {
			const response = await fetch(config.ajaxUrl, {
				method: "POST",
				credentials: "same-origin",
				body: new FormData(form),
			});

			const payload = await response.json();

			if (payload.success) {
				setMessage(messageTarget, payload.data?.message || "", "success");
				form.reset();
				return;
			}

			setMessage(
				messageTarget,
				payload.data?.message || i18n.error || "Something went wrong. Please try again.",
				"error"
			);

			if (isContactForm && payload.data?.field) {
				setFieldError(form, payload.data.field, payload.data.message || "");
			}
		} catch (error) {
			setMessage(messageTarget, i18n.error || "Something went wrong. Please try again.", "error");
		} finally {
			setSubmittingState(form, false);
		}
	}

	function closeSearch() {
		if (!searchWrap || !searchToggle || !searchForm || !searchInput) {
			return;
		}

		searchWrap.classList.remove("search-open");
		searchToggle.setAttribute("aria-expanded", "false");
		searchToggle.setAttribute("aria-label", i18n.openSearch || "Open search");
		searchForm.setAttribute("aria-hidden", "true");
		searchInput.setAttribute("tabindex", "-1");
	}

	function openSearch() {
		if (!searchWrap || !searchToggle || !searchForm || !searchInput) {
			return;
		}

		searchWrap.classList.add("search-open");
		searchToggle.setAttribute("aria-expanded", "true");
		searchToggle.setAttribute("aria-label", i18n.closeSearch || "Close search");
		searchForm.setAttribute("aria-hidden", "false");
		searchInput.removeAttribute("tabindex");
		window.setTimeout(() => searchInput.focus(), 20);
	}

	function toggleSearch() {
		if (!searchWrap) {
			return;
		}

		if (searchWrap.classList.contains("search-open")) {
			closeSearch();
		} else {
			openSearch();
		}
	}

	function closeNav() {
		if (!header || !navToggle) {
			return;
		}

		header.classList.remove("nav-open");
		document.body.classList.remove("ca-lock-scroll");
		navToggle.setAttribute("aria-expanded", "false");
		navToggle.setAttribute("aria-label", i18n.openMenu || "Open navigation menu");
	}

	function openNav() {
		if (!header || !navToggle) {
			return;
		}

		header.classList.add("nav-open");
		if (mobileBreakpoint.matches) {
			document.body.classList.add("ca-lock-scroll");
		}
		navToggle.setAttribute("aria-expanded", "true");
		navToggle.setAttribute("aria-label", i18n.closeMenu || "Close navigation menu");
	}

	function toggleNav() {
		if (!header) {
			return;
		}

		if (header.classList.contains("nav-open")) {
			closeNav();
		} else {
			openNav();
		}
	}

	function updateHeaderState() {
		if (!header) {
			return;
		}

		header.classList.toggle("is-scrolled", window.scrollY > 12);
	}

	function fallbackCopy(text) {
		const input = document.createElement("input");
		input.value = text;
		document.body.appendChild(input);
		input.select();
		document.execCommand("copy");
		input.remove();
	}

	async function copyLink(button) {
		const url = button.getAttribute("data-url");
		if (!url) {
			return;
		}

		try {
			if (navigator.clipboard && navigator.clipboard.writeText) {
				await navigator.clipboard.writeText(url);
			} else {
				fallbackCopy(url);
			}

			const label = button.querySelector(".ca-share__text");
			if (!label) {
				return;
			}

			const original = label.textContent;
			label.textContent = i18n.copied || "Copied!";
			window.setTimeout(() => {
				label.textContent = original || i18n.copyLink || "Copy link";
			}, 1800);
		} catch (error) {
			fallbackCopy(url);
		}
	}

	function updateReadingProgress() {
		if (!progressBar || !document.body.classList.contains("single-article")) {
			return;
		}

		const article = document.querySelector(".ca-article-content");
		if (!article) {
			return;
		}

		const articleRect = article.getBoundingClientRect();
		const articleTop = articleRect.top + window.scrollY;
		const articleHeight = article.offsetHeight;
		const windowHeight = window.innerHeight;
		const maxScroll = Math.max(articleHeight - windowHeight * 0.45, 1);
		const progress = Math.min(Math.max((window.scrollY - articleTop + windowHeight * 0.2) / maxScroll, 0), 1);

		progressBar.style.width = `${progress * 100}%`;
	}

	if (searchToggle) {
		searchToggle.addEventListener("click", toggleSearch);
	}

	if (navToggle) {
		navToggle.addEventListener("click", toggleNav);
	}

	document.addEventListener("click", (event) => {
		if (searchWrap && !searchWrap.contains(event.target)) {
			closeSearch();
		}

		if (header && !header.contains(event.target) && mobileBreakpoint.matches) {
			closeNav();
		}

		const copyButton = event.target.closest(".ca-share__btn--copy");
		if (copyButton) {
			event.preventDefault();
			copyLink(copyButton);
		}
	});

	document.addEventListener("keydown", (event) => {
		if (event.key !== "Escape") {
			return;
		}

		closeSearch();
		closeNav();
	});

	function handleBreakpointChange(event) {
		if (!event.matches) {
			document.body.classList.remove("ca-lock-scroll");
			closeNav();
		}
	}

	if (typeof mobileBreakpoint.addEventListener === "function") {
		mobileBreakpoint.addEventListener("change", handleBreakpointChange);
	} else if (typeof mobileBreakpoint.addListener === "function") {
		mobileBreakpoint.addListener(handleBreakpointChange);
	}

	window.addEventListener("scroll", () => {
		updateHeaderState();
		updateReadingProgress();
	}, { passive: true });

	window.addEventListener("resize", updateReadingProgress);

	document.querySelectorAll(".ca-nl-form").forEach((form) => {
		form.addEventListener("submit", (event) => {
			event.preventDefault();
			const messageTarget = form.closest(".ca-nl-form-wrap")?.querySelector(".ca-nl-msg");
			sendAjaxForm(form, messageTarget, false);
		});
	});

	const contactForm = document.getElementById("ca-contact-form");
	if (contactForm) {
		contactForm.addEventListener("submit", (event) => {
			event.preventDefault();
			sendAjaxForm(contactForm, document.getElementById("ca-contact-msg"), true);
		});
	}

	updateHeaderState();
	updateReadingProgress();
})();
