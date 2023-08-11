 <script>
        // Function to update the clock
        function updateClock() {
            var now = new Date();
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var seconds = now.getSeconds();

            // Add leading zeros if needed
            hours = hours.toString().padStart(2, '0');
            minutes = minutes.toString().padStart(2, '0');
            seconds = seconds.toString().padStart(2, '0');

            var time = hours + ':' + minutes + ':' + seconds;

            // Update the clock element
            document.getElementById('clock').textContent = time;

            // Schedule the next update
            setTimeout(updateClock, 1000);
        }

        // Initial call to start the clock
        updateClock();
    </script>