@php
    $terms = trim((string) ($terms ?? ''));
    $formatTermsInline = function ($text) {
        $escaped = e($text);
        $escaped = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $escaped);
        $escaped = preg_replace('/__(.*?)__/', '<strong>$1</strong>', $escaped);
        $escaped = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $escaped);

        return $escaped;
    };
    $renderPlainTerms = function ($text) use ($formatTermsInline) {
        $lines = preg_split('/\R/u', trim((string) $text));
        $html = '';
        $openList = null;
        $closeList = function () use (&$html, &$openList) {
            if ($openList) {
                $html .= "</{$openList}>";
                $openList = null;
            }
        };

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                $closeList();
                continue;
            }

            if (preg_match('/^[-*•]\s+(.+)$/u', $line, $matches)) {
                if ($openList !== 'ul') {
                    $closeList();
                    $html .= '<ul>';
                    $openList = 'ul';
                }

                $html .= '<li>' . $formatTermsInline($matches[1]) . '</li>';
                continue;
            }

            if (preg_match('/^\d+[\.)]\s+(.+)$/u', $line, $matches)) {
                if ($openList !== 'ol') {
                    $closeList();
                    $html .= '<ol>';
                    $openList = 'ol';
                }

                $html .= '<li>' . $formatTermsInline($matches[1]) . '</li>';
                continue;
            }

            $closeList();
            $html .= '<p>' . $formatTermsInline($line) . '</p>';
        }

        $closeList();

        return $html;
    };
    $termsHtml = $terms && $terms !== strip_tags($terms) ? $terms : $renderPlainTerms($terms);
@endphp

{!! $termsHtml !!}
