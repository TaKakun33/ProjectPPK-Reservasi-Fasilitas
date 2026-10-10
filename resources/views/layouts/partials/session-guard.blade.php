{{-- Skrip penjaga sesi per tab: logout otomatis jika tab baru dibuka tanpa login di tab ini --}}
@auth
    <script>
        (function() {
            @if(session('just_logged_in'))
                sessionStorage.setItem('tab_session_active', '1');
            @endif

            if (!sessionStorage.getItem('tab_session_active')) {
                // Jika tab baru dibuka terpisah (setelah tab sebelumnya diclose),
                // otomatis logout dan arahkan kembali ke home daftar fasilitas.
                fetch("{{ route('logout') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    }
                }).finally(function() {
                    window.location.replace("{{ route('welcome') }}");
                });
            } else {
                sessionStorage.setItem('tab_session_active', '1');
            }
        })();
    </script>
@endauth
