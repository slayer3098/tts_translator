@extends('layouts.app')

@section('title', 'TTS Translator - Home')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Translation Form -->
    <div class="lg:col-span-2">
        <div class="glass-effect rounded-xl p-6 shadow-xl">
            <h2 class="text-2xl font-bold text-white mb-6">
                <i class="fas fa-language mr-2"></i>
                Translate & Speak
            </h2>

            <form id="translationForm" class="space-y-6">
                @csrf
                <!-- Text Input -->
                <div>
                    <label class="block text-white text-sm font-medium mb-2">
                        <i class="fas fa-edit mr-1"></i>
                        Enter your text
                    </label>
                    <textarea 
                        id="inputText" 
                        name="text" 
                        rows="4" 
                        class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-gray-300 border border-white border-opacity-30 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                        placeholder="Type your text here..."
                        required
                    ></textarea>
                </div>

                <!-- Language Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-white text-sm font-medium mb-2">
                            <i class="fas fa-globe mr-1"></i>
                            Target Language
                        </label>
                        <select 
                            id="targetLanguage" 
                            name="target_language" 
                            class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-20 text-white border border-white border-opacity-30 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            required
                        >
                            @foreach($languages as $code => $name)
                                @if($code !== 'en')
                                    <option value="{{ $code }}" class="text-gray-800">{{ $name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-white text-sm font-medium mb-2">
                            <i class="fas fa-user mr-1"></i>
                            Voice Type
                        </label>
                        <select 
                            id="voiceType" 
                            name="voice_type" 
                            class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-20 text-white border border-white border-opacity-30 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            required
                        >
                            <option value="female" class="text-gray-800">Female</option>
                            <option value="male" class="text-gray-800">Male</option>
                        </select>
                    </div>
                </div>

                <!-- Voice Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-white text-sm font-medium mb-2">
                            <i class="fas fa-music mr-1"></i>
                            Pitch: <span id="pitchValue">1.0</span>
                        </label>
                        <input 
                            type="range" 
                            id="pitch" 
                            name="pitch" 
                            min="0.5" 
                            max="2.0" 
                            step="0.1" 
                            value="1.0"
                            class="w-full h-2 bg-white bg-opacity-20 rounded-lg appearance-none cursor-pointer"
                        >
                    </div>

                    <div>
                        <label class="block text-white text-sm font-medium mb-2">
                            <i class="fas fa-tachometer-alt mr-1"></i>
                            Speed: <span id="speedValue">1.0</span>
                        </label>
                        <input 
                            type="range" 
                            id="speed" 
                            name="speed" 
                            min="0.5" 
                            max="2.0" 
                            step="0.1" 
                            value="1.0"
                            class="w-full h-2 bg-white bg-opacity-20 rounded-lg appearance-none cursor-pointer"
                        >
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button 
                        type="submit" 
                        id="translateBtn"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg"
                    >
                        <i class="fas fa-magic mr-2"></i>
                        Translate & Speak
                    </button>
                </div>
            </form>

            <!-- Loading Indicator -->
            <div id="loading" class="hidden text-center mt-6">
                <div class="loader mx-auto"></div>
                <p class="text-white mt-2">Processing...</p>
            </div>

            <!-- Results -->
            <div id="results" class="hidden mt-8 p-6 glass-effect rounded-lg">
                <h3 class="text-lg font-semibold text-white mb-4">
                    <i class="fas fa-check-circle mr-2"></i>
                    Translation Result
                </h3>
                <div id="translatedText" class="text-white text-lg mb-4 p-4 bg-white bg-opacity-10 rounded-lg"></div>
                
                <div class="flex flex-wrap gap-4 justify-center">
                    <button 
                        id="playBtn" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition duration-300"
                    >
                        <i class="fas fa-play mr-2"></i>
                        Play Audio
                    </button>
                    <button 
                        id="pauseBtn" 
                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg transition duration-300 hidden"
                    >
                        <i class="fas fa-pause mr-2"></i>
                        Pause
                    </button>
                    <button 
                        id="stopBtn" 
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition duration-300"
                    >
                        <i class="fas fa-stop mr-2"></i>
                        Stop
                    </button>
                    <button 
                        id="downloadBtn" 
                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition duration-300"
                    >
                        <i class="fas fa-download mr-2"></i>
                        Download Audio
                    </button>
                </div>
            </div>

            <!-- Error Display -->
            <div id="error" class="hidden mt-6 p-4 bg-red-500 bg-opacity-20 border border-red-500 text-red-100 rounded-lg">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span id="errorMessage"></span>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Features -->
        <div class="glass-effect rounded-xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-white mb-4">
                <i class="fas fa-star mr-2"></i>
                Features
            </h3>
            <ul class="space-y-3 text-white text-sm">
                <li><i class="fas fa-check text-green-400 mr-2"></i>Multi-language translation</li>
                <li><i class="fas fa-check text-green-400 mr-2"></i>Text-to-speech conversion</li>
                <li><i class="fas fa-check text-green-400 mr-2"></i>Voice customization</li>
                <li><i class="fas fa-check text-green-400 mr-2"></i>Audio download</li>
                <li><i class="fas fa-check text-green-400 mr-2"></i>Translation history</li>
            </ul>
        </div>

        <!-- Recent History -->
        @if($history->count() > 0)
        <div class="glass-effect rounded-xl p-6 shadow-xl">
            <h3 class="text-lg font-semibent text-white mb-4">
                <i class="fas fa-history mr-2"></i>
                Recent Translations
            </h3>
            <div class="space-y-3">
                @foreach($history->take(5) as $item)
                <div class="text-sm text-white bg-white bg-opacity-10 p-3 rounded-lg">
                    <div class="font-medium">{{ Str::limit($item->original_text, 30) }}</div>
                    <div class="text-gray-300 text-xs mt-1">
                        EN → {{ strtoupper($item->target_language) }}
                    </div>
                </div>
                @endforeach
            </div>
            <a href="{{ route('translations.history') }}" class="block text-center text-blue-300 hover:text-blue-200 mt-4 text-sm">
                View all history →
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentUtterance = null;
let currentTranslation = null;

// Update slider values display
document.getElementById('pitch').addEventListener('input', function() {
    document.getElementById('pitchValue').textContent = this.value;
});

document.getElementById('speed').addEventListener('input', function() {
    document.getElementById('speedValue').textContent = this.value;
});

// Form submission
document.getElementById('translationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Show loading
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('results').classList.add('hidden');
    document.getElementById('error').classList.add('hidden');
    document.getElementById('translateBtn').disabled = true;
    
    try {
        const response = await axios.post('/translate', data);
        
        if (response.data.success) {
            currentTranslation = response.data.translation;
            document.getElementById('translatedText').textContent = response.data.translated_text;
            document.getElementById('results').classList.remove('hidden');
            
            // Auto-play the translation
            speakText(response.data.translated_text, data.target_language, data.voice_type, data.pitch, data.speed);
        } else {
            showError(response.data.message || 'Translation failed');
        }
    } catch (error) {
        showError(error.response?.data?.message || 'An error occurred during translation');
    } finally {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('translateBtn').disabled = false;
    }
});

function speakText(text, language, voiceType, pitch, speed) {
    if ('speechSynthesis' in window) {
        speechSynthesis.cancel();
        
        currentUtterance = new SpeechSynthesisUtterance(text);
        
        const voices = speechSynthesis.getVoices();
        const voice = voices.find(v => 
            v.lang.startsWith(language) && 
            (voiceType === 'female' ? v.name.toLowerCase().includes('female') : v.name.toLowerCase().includes('male'))
        ) || voices.find(v => v.lang.startsWith(language)) || voices[0];
        
        if (voice) currentUtterance.voice = voice;
        currentUtterance.pitch = parseFloat(pitch);
        currentUtterance.rate = parseFloat(speed);
        
        currentUtterance.onstart = () => {
            document.getElementById('playBtn').classList.add('hidden');
            document.getElementById('pauseBtn').classList.remove('hidden');
        };
        
        currentUtterance.onend = () => {
            document.getElementById('playBtn').classList.remove('hidden');
            document.getElementById('pauseBtn').classList.add('hidden');
        };
        
        speechSynthesis.speak(currentUtterance);
    } else {
        showError('Speech synthesis not supported in this browser');
    }
}

// Audio controls
document.getElementById('playBtn').addEventListener('click', function() {
    const translatedText = document.getElementById('translatedText').textContent;
    const formData = new FormData(document.getElementById('translationForm'));
    const data = Object.fromEntries(formData);
    
    speakText(translatedText, data.target_language, data.voice_type, data.pitch, data.speed);
});

document.getElementById('pauseBtn').addEventListener('click', function() {
    if (speechSynthesis.speaking) {
        speechSynthesis.pause();
        this.classList.add('hidden');
        document.getElementById('playBtn').classList.remove('hidden');
    }
});

document.getElementById('stopBtn').addEventListener('click', function() {
    speechSynthesis.cancel();
    document.getElementById('playBtn').classList.remove('hidden');
    document.getElementById('pauseBtn').classList.add('hidden');
});

document.getElementById('downloadBtn').addEventListener('click', function() {
    if (currentTranslation) {
        window.location.href = `/download/${currentTranslation.id}`;
    }
});

function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('error').classList.remove('hidden');
}

// Load voices when available
if ('speechSynthesis' in window) {
    speechSynthesis.addEventListener('voiceschanged', function() {
        // Voices loaded
    });
}
</script>
@endsection
