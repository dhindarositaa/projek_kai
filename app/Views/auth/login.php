<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <!-- Tailwind CDN for quick demo (replace with compiled CSS in production) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      /* small custom styles to mimic the original visuals */
      .bg-ink {
        background-color: #1f2937;
      } /* dark navy */
      .card-bg {
        background-color: #e6eef3;
      }
      .accent-1 {
        stroke: #2dd4bf;
      }
      .accent-2 {
        stroke: #fb923c;
      }
      .accent-3 {
        stroke: #ef4444;
      }
      .accent-4 {
        stroke: #60a5fa;
      }
    </style>
  </head>
  <body class="min-h-screen bg-ink text-gray-100 flex flex-col">

    <!-- Page main area -->
    <main class="flex-1 grid grid-cols-1 md:grid-cols-3 items-center">
      <!-- left decorative SVG -->
      <div class="hidden md:flex items-center justify-center">
        <svg
          width="260"
          height="520"
          viewBox="0 0 260 520"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <g stroke-linecap="round" stroke-linejoin="round" stroke-width="8">
            <path
              d="M40 40a40 40 0 1 1 0 80"
              class="accent-4"
              fill="none"
              stroke="#34d399"
            />
            <path d="M20 200v80" stroke="#fb923c" />
            <path d="M60 320a40 40 0 1 1 0 80" stroke="#2dd4bf" />
            <path d="M10 430a40 40 0 1 1 0 80" stroke="#60a5fa" />
          </g>
        </svg>
      </div>

      <!-- center card -->
      <div class="md:col-span-1 flex justify-center">
        <div class="w-full max-w-md mx-6">
          <div class="rounded-lg shadow-lg card-bg p-8">
            <h3 class="text-center text-gray-600 font-semibold">Sign in</h3>
            <hr class="my-6 border-gray-200" />

            <form class="space-y-4">
              <div>
                <label class="block text-xs text-gray-500 font-semibold"
                  >EMAIL</label
                >
                <input
                  type="email"
                  placeholder="Email"
                  class="mt-1 w-full rounded-md px-3 py-2 text-gray-700 focus:outline-none"
                />
              </div>

              <div>
                <label class="block text-xs text-gray-500 font-semibold"
                  >PASSWORD</label
                >
                <input
                  type="password"
                  placeholder="Password"
                  class="mt-1 w-full rounded-md px-3 py-2 text-gray-700 focus:outline-none"
                />
              </div>

              <div class="flex items-center gap-2">
                <input id="remember" type="checkbox" class="h-4 w-4" />
                <label for="remember" class="text-sm text-gray-600"
                  >Remember me</label
                >
              </div>

              <button
                type="submit"
                class="mt-2 w-full bg-gray-900 text-white py-3 rounded-md font-semibold"
              >
                SIGN IN
              </button>
            </form>

            <div class="flex justify-between mt-6 text-sm text-gray-500">
              <a href="<?= site_url('register') ?>" class="hover:text-gray-800">Create new account</a>
            </div>
          </div>
        </div>
      </div>

      <!-- right decorative SVG -->
      <div class="hidden md:flex items-center justify-center">
        <svg
          width="260"
          height="520"
          viewBox="0 0 260 520"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <g stroke-linecap="round" stroke-linejoin="round" stroke-width="8">
            <path d="M220 40a40 40 0 1 0 0 80" stroke="#fb923c" />
            <path d="M180 200h60" stroke="#2dd4bf" />
            <path d="M200 320a40 40 0 1 0 0 80" stroke="#10b981" />
            <path d="M210 430a40 40 0 1 0 0 80" stroke="#ef4444" />
          </g>
        </svg>
      </div>
    </main>

    <!-- Footer -->
    <footer
      class="border-t border-gray-700 py-6 px-8 text-gray-400 text-sm"
    ></footer>
  </body>
</html>
