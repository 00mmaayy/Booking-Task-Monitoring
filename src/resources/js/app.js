import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
	const modalRoot = document.createElement('div');
	modalRoot.id = 'global-confirm-modal';
	modalRoot.className = 'fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 p-4';
	modalRoot.innerHTML = `
		<div class="w-full max-w-[18rem] rounded-md bg-white p-3 shadow-xl">
			<h3 class="text-lg font-semibold text-gray-900">Confirm Submission</h3>
			<p id="global-confirm-message" class="mt-2 text-sm text-gray-600"></p>
			<div class="mt-4 flex justify-end gap-2">
				<button id="global-confirm-cancel" type="button" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Cancel</button>
				<button id="global-confirm-submit" type="button" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">Confirm</button>
			</div>
		</div>
	`;

	document.body.appendChild(modalRoot);

	const messageEl = modalRoot.querySelector('#global-confirm-message');
	const cancelButton = modalRoot.querySelector('#global-confirm-cancel');
	const confirmButton = modalRoot.querySelector('#global-confirm-submit');

	let pendingForm = null;

	const closeModal = () => {
		modalRoot.classList.add('hidden');
		modalRoot.classList.remove('flex');
		pendingForm = null;
	};

	cancelButton?.addEventListener('click', closeModal);

	modalRoot.addEventListener('click', (event) => {
		if (event.target === modalRoot) {
			closeModal();
		}
	});

	confirmButton?.addEventListener('click', () => {
		if (!pendingForm) {
			return;
		}

		const formToSubmit = pendingForm;
		closeModal();
		formToSubmit.submit();
	});

	document.querySelectorAll('form[data-confirm]').forEach((form) => {
		form.addEventListener('submit', (event) => {
			if (form.dataset.skipConfirm === 'true') {
				return;
			}

			event.preventDefault();
			pendingForm = form;

			if (messageEl) {
				messageEl.textContent = form.getAttribute('data-confirm') ?? 'Are you sure you want to submit this form?';
			}

			modalRoot.classList.remove('hidden');
			modalRoot.classList.add('flex');
		});
	});
});
