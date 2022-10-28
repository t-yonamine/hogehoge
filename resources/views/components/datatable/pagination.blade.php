@props(['paginator', 'showTotal' => false])
@if ($paginator->isNotEmpty())
    @if ($showTotal)
        <div>
            {{ $paginator->total() .
                '件中' .
                (($paginator->currentPage() - 1) * $paginator->perPage() + 1) .
                '～' .
                ($paginator->lastPage() == $paginator->currentPage()
                    ? $paginator->total()
                    : $paginator->currentPage() * $paginator->perPage()) .
                '件表示' }}
        </div>
    @endif
    {{ $slot }}
    <div class="row">
        <div class="col-md-12 d-flex justify-content-center">
            <nav>
                <ul class="pagination">
                    @foreach ($paginator->withQueryString()->linkCollection() as $link)
                        <li @class([
                            'page-item',
                            'active' => $link['active'],
                            'disabled' => !$link['url'],
                        ])>
                            @php
                                $label = '';
                                if ($loop->last) {
                                    $label = 'fa fa-solid fa-angle-right';
                                } elseif ($loop->first) {
                                    $label = 'fa fa-solid fa-angle-left';
                                } else {
                                    $label = $link['label'];
                                }
                            @endphp
                            @if ($link['active'])
                                <span @class(['page-link'])>
                                    {{ $link['label'] }}
                                </span>
                            @else
                                <a href="{{ $link['url'] ?? '#' }}" tabindex="0" @class(['page-link'])>
                                    @if (is_numeric($label))
                                        {{ $label }}
                                    @else
                                        <i class='{{ $label }}'></i>
                                    @endif
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </div>
@else
{{$slot}}
@endif
