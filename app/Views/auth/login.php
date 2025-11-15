<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <link href="<?= base_url('css/tailwind.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      .bg-ink {
        background-color: #1f2937;
      }
      .card-bg {
        background-color: #e6eef3;
      }
      .error-input {
        border-color: #ef4444;
      }
      .error-text {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
      }
    </style>
  </head>
  <body class="min-h-screen bg-ink text-gray-100 flex flex-col">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
      <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
        <span class="text-gray-700">Memproses...</span>
      </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
      <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
        <div class="text-center">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Error!</h3>
          <p class="text-gray-600 mb-4" id="errorMessage"></p>
          <button onclick="hideErrorModal()" class="w-full bg-red-600 text-white py-2 rounded-md font-semibold hover:bg-red-700 transition duration-200">
            Tutup
          </button>
        </div>
      </div>
    </div>

    <main class="flex-1 grid grid-cols-1 md:grid-cols-3 items-start md:items-center">
      <!-- left decorative -->
      <div class="hidden md:flex items-center justify-center">
        <svg width="260" height="520" viewBox="0 0 260 520" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g stroke-linecap="round" stroke-linejoin="round" stroke-width="8">
            <path d="M40 40a40 40 0 1 1 0 80" stroke="#60a5fa" />
            <path d="M20 200v80" stroke="#fb923c" />
            <path d="M60 320a40 40 0 1 1 0 80" stroke="#2dd4bf" />
            <path d="M10 430a40 40 0 1 1 0 80" stroke="#10b981" />
          </g>
        </svg>
      </div>

      <!-- center card -->
      <div class="md:col-span-1 flex justify-center mt-12 md:mt-0">
        <div class="w-full max-w-md mx-6">
          <div class="rounded-lg shadow-lg card-bg p-8">
            <h3 class="text-center text-gray-600 font-semibold text-2xl mb-2">Sign In</h3>
            <p class="text-center text-gray-500 text-sm mb-6">Masuk ke akun Anda</p>

            <hr class="my-6 border-gray-200" />

            <form id="loginForm" class="space-y-4">
              <div>
                <label class="block text-xs text-gray-500 font-semibold">EMAIL</label>
                <input
                  type="email"
                  name="email"
                  id="email"
                  placeholder="Email"
                  class="mt-1 w-full rounded-md px-3 py-2 text-gray-700 focus:outline-none border border-gray-300"
                  required
                />
              </div>

              <div>
                <label class="block text-xs text-gray-500 font-semibold">PASSWORD</label>
                <input
                  type="password"
                  name="password"
                  id="password"
                  placeholder="Password"
                  class="mt-1 w-full rounded-md px-3 py-2 text-gray-700 focus:outline-none border border-gray-300"
                  required
                />
              </div>

              <button
                type="submit"
                class="mt-2 w-full bg-gray-900 text-white py-3 rounded-md font-semibold hover:bg-gray-800 transition duration-200"
              >
                SIGN IN
              </button>
            </form>

            <div class="flex justify-between mt-6 text-sm text-gray-500">
              <p>
                Don't have account?
                <a href="<?= site_url('register') ?>" class="text-blue-500 hover:underline">Sign Up</a>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- right decorative -->
      <div class="hidden md:flex items-center justify-center">
        <svg width="260" height="520" viewBox="0 0 260 520" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g stroke-linecap="round" stroke-linejoin="round" stroke-width="8">
            <path d="M220 40a40 40 0 1 0 0 80" stroke="#fb923c" />
            <path d="M180 200h60" stroke="#2dd4bf" />
            <path d="M200 320a40 40 0 1 0 0 80" stroke="#10b981" />
            <path d="M210 430a40 40 0 1 0 0 80" stroke="#ef4444" />
          </g>
        </svg>
      </div>
    </main>

    <footer class="border-t border-gray-700 py-6 px-8 text-gray-400 text-sm text-center">
      &copy; <?= date('Y') ?> Your Company. All rights reserved.
    </footer>

    <script>
      function showLoading() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
      }

      function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('hidden');
      }

      function showErrorModal(message) {
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorModal').classList.remove('hidden');
      }

      function hideErrorModal() {
        document.getElementById('errorModal').classList.add('hidden');
      }

      document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        loginUser();
      });

      function loginUser() {
        showLoading();
        
        const formData = new FormData(document.getElementById('loginForm'));
        
        fetch('<?= site_url('auth/processLogin') ?>', {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          hideLoading();
          
          if (data.status === 'success') {
            window.location.href = data.redirect;
          } else {
            showErrorModal(data.message);
          }
        })
        .catch(error => {
          hideLoading();
          showErrorModal('Terjadi kesalahan jaringan. Silakan coba lagi.');
          console.error('Error:', error);
        });
      }
    </script>
  </body>
</html>