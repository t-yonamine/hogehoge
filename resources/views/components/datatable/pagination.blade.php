@props([
'paginator',
'showTotal' => false
])
@if ($paginator->isNotEmpty())
    @if($showTotal)
    <div>{{ $paginator->total(). '件中' . (($paginator->currentPage() - 1) * $paginator->perPage() + 1) .'～'.
        ( $paginator->lastPage() == $paginator->currentPage() ? $paginator->total() : $paginator->currentPage() * $paginator->perPage()) .'件表示' }}</div>
    @endif
    {{$slot}}
    <div class="row">
        <div class="col-md-12 d-flex justify-content-center">
            <nav>
                <ul class="pagination">
                    @foreach ($paginator->withQueryString()->linkCollection() as $link)
                    <li @class([ 'page-item' , 'active'=> $link['active'],
                        'disabled' => !$link['url'],
                        ])>
                        @php
                        $label = '';
                        if ($loop->last) {
                        $label = '前';
                        } elseif ($loop->first)
                        $label = '後';
                        else {
                        $label = $link['label'];
                        }
                        @endphp
                        @if($link['active'])
                        <span @class(['page-link'])>
                            {{ $label }}
                        </span>
                        @else
                        <a href="{{ $link['url'] ?? '#' }}" tabindex="0" @class(['page-link'])>
                            {{ $label }}
                        </a>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </div>
@endif