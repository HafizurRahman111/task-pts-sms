<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send SMS</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- jQuery and Select2 CSS & JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>

<body class="bg-gray-100 font-sans">

    <div class="container mx-auto p-6">
        <!-- Heading -->
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">Send SMS</h1>

        <!-- Card -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <form action="{{ route('sms.send') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Subject Field -->
                <div>
                    <label for="subject" class="block text-lg font-medium text-gray-800">Subject:</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                        class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <!-- Message Field -->
                <div class="mt-4">
                    <label for="message" class="block text-lg font-medium text-gray-800">Message:</label>
                    <textarea id="message" name="message" rows="4" required
                        class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('message') }}</textarea>
                </div>

                <!-- User Selection -->
                <div class="mt-6">
                    <label for="user_ids" class="block text-lg font-medium text-gray-800">Select Users:</label>
                    <select name="user_ids[]" id="user_ids" multiple
                        class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-300 mt-6">
                    Send Message
                </button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#user_ids').select2({
                placeholder: "Select users",
                allowClear: true
            });
        });
    </script>

</body>

</html>
