@extends('layouts.app')

@section('title', 'Translation History')

@section('content')
<div class="glass-effect rounded-xl p-6 shadow-xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">
            <i class="fas fa-history mr-2"></i>
            Translation History
        </h2>
        <a href="{{ route('translations.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-300">
            <i class="fas fa-plus mr-2"></i>
            New Translation
        </a>
    </div>

    @if($translations->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-white">
                <thead>
                    <tr class="border-b border-white border-opacity-20">
                        <th class="text-left py-3 px-4">Original Text</th>
                        <th class="text-left py-3 px-4">Language</th>
                        <th class="text-left py-3 px-4">Translated Text</th>
                        <th class="text-left py-3 px-4">Voice</th>
                        <th class="text-left py-3 px-4">Date</th>
                        <th class="text-left py-3 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($translations as $translation)
                    <tr class="border-b border-white border-opacity-10 hover:bg-white hover:bg-opacity-5 transition duration-200">
                        <td class="py-4 px-4">
                            <div class="max-w-xs truncate" title="{{ $translation->original_text }}">
                                {{ Str::limit($translation->original_text, 50) }}
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                EN → {{ strtoupper($translation->target_language) }}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="max-w-xs truncate" title="{{ $translation->translated_text }}">
                                {{ Str::limit($translation->translated_text, 50) }}
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="text-sm">
                                <div>{{ ucfirst($translation->voice_type) }}</div>
                                <div class="text-gray-300 text-xs">
                                    P: {{ $translation->pitch }} | S: {{ $translation->speed }}
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-sm">
                            {{ $translation->created_at->format('M j, Y') }}
                            <div class="text-xs text-gray-300">
                                {{ $translation->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex space-x-2">
                                <button 
                                    onclick="playTranslation({{ json_encode($translation) }})"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition duration-200"
                                    title="Play Audio"
                                >
                                    <i class="fas fa-play"></i>
                                </button>
                                <button 
                                    onclick="copyToClipboard('{{ addslashes($translation->translated_text) }}')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition duration-200"
                                    title="Copy Translation"
                                >
                                    <i class="fas fa-copy"></i>
                                </button>
                                <a 
                                    href="{{ route('translations.download', $translation->id) }}"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-xs transition duration-200"
                                    title="Download Translation"
                                >
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-white">
            {{ $translations->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-history text-6xl text-white text-opacity-50 mb-4"></i>
            <h3 class="text-xl text-white mb-2">No translations yet</h3>
            <p class="text-gray-300 mb-6">Start by creating your first translation!</p>
            <a href="{{ route('translations.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-300">
                <i class="fas fa-plus mr-2"></i>
                Create Translation
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function playTranslation(translation) {
    if ('speechSynthesis' in window) {
        speechSynthesis.cancel();
        
        const utterance = new SpeechSynthesisUtterance(translation.translated_text);
        
        // Find appropriate voice
        const voices = speechSynthesis.getVoices();
        const voice = voices.find(v => 
            v.lang.startsWith(translation.target_language) && 
            (translation.voice_type === 'female' ? v.name.toLowerCase().includes('female') : v.name.toLowerCase().includes('male'))
        ) || voices.find(v => v.lang.startsWith(translation.target_language)) || voices[0];
        
        if (voice) utterance.voice = voice;
        utterance.pitch = parseFloat(translation.pitch);
        utterance.rate = parseFloat(translation.speed);
        
        speechSynthesis.speak(utterance);
        
        // Visual feedback
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-volume-up"></i>';
        
        utterance.onend = () => {
            button.innerHTML = originalHTML;
        };
    } else {
        alert('Speech synthesis not supported in this browser');
    }
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Copied to clipboard!', 'success');
        }, function(err) {
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showToast('Copied to clipboard!', 'success');
        } else {
            showToast('Failed to copy text', 'error');
        }
    } catch (err) {
        showToast('Failed to copy text', 'error');
    }

    document.body.removeChild(textArea);
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 text-white transition-all duration-300 transform translate-x-full`;
    toast.className += type === 'success' ? ' bg-green-500' : ' bg-red-500';
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'exclamation'} mr-2"></i>${message}`;
    
    document.body.appendChild(toast);
    
    // Slide in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Slide out after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Load voices when available
if ('speechSynthesis' in window) {
    speechSynthesis.addEventListener('voiceschanged', function() {
        // Voices loaded
    });
}
</script>
@endsection