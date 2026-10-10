@props(['size' => 'md'])

@php
    $dim = match ($size) {
        'sm' => 'w-7 h-7',
        'lg' => 'w-12 h-12',
        'xl' => 'w-16 h-16',
        default => 'w-9 h-9',
    };
@endphp

<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => $dim]) }}>
    <defs>
        <linearGradient id="uniMane" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%"   stop-color="#FF7AC6" />
            <stop offset="33%"  stop-color="#A78BFA" />
            <stop offset="66%"  stop-color="#7DD3FC" />
            <stop offset="100%" stop-color="#86EFAC" />
        </linearGradient>
        <linearGradient id="uniHorn" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%"   stop-color="#FCD34D" />
            <stop offset="100%" stop-color="#F59E0B" />
        </linearGradient>
        <radialGradient id="uniBody" cx="50%" cy="50%" r="50%">
            <stop offset="0%"   stop-color="#FFFFFF" />
            <stop offset="100%" stop-color="#FFE4F1" />
        </radialGradient>
    </defs>

    <!-- Sparkles -->
    <g fill="#FCD34D">
        <circle cx="10" cy="14" r="1.2"/>
        <circle cx="54" cy="22" r="1"/>
        <circle cx="50" cy="52" r="1.4"/>
        <circle cx="14" cy="48" r="1"/>
    </g>

    <!-- Mane (rainbow) -->
    <path d="M14 28 Q14 12 32 12 Q50 12 50 28 L48 24 Q44 18 38 18 L40 14 Q34 18 30 16 L28 22 Q22 18 22 24 Z"
          fill="url(#uniMane)" />

    <!-- Body / Head -->
    <ellipse cx="32" cy="36" rx="16" ry="14" fill="url(#uniBody)" stroke="#F9A8D4" stroke-width="1.2"/>

    <!-- Horn -->
    <path d="M32 6 L29 22 L35 22 Z" fill="url(#uniHorn)" stroke="#D97706" stroke-width="0.6" stroke-linejoin="round"/>
    <path d="M30.5 14 L33.5 14" stroke="#FFFFFF" stroke-width="0.8" stroke-linecap="round" opacity="0.7"/>

    <!-- Ear -->
    <path d="M22 22 L20 14 L26 18 Z" fill="#FFB1D8" stroke="#F472B6" stroke-width="0.8"/>

    <!-- Eye -->
    <circle cx="36" cy="34" r="2.5" fill="#312E81"/>
    <circle cx="37" cy="33" r="0.8" fill="#FFF"/>

    <!-- Cheek blush -->
    <ellipse cx="42" cy="40" rx="2.5" ry="1.5" fill="#FF7AC6" opacity="0.5"/>

    <!-- Smile -->
    <path d="M38 41 Q40 43 43 41" stroke="#9D174D" stroke-width="1" fill="none" stroke-linecap="round"/>

    <!-- Tail flick (sparkle accent) -->
    <path d="M16 38 Q10 40 12 46" stroke="url(#uniMane)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
</svg>
