@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginação" class="flex items-center justify-between mt-6">
        <div>
            <p class="text-sm text-gray-700 leading-5">
                Exibindo <span class="font-medium">{{ $paginator->firstItem() }}</span>
                a <span class="font-medium">{{ $paginator->lastItem() }}</span>
                de <span class="font-medium">{{ $paginator->total() }}</span> resultados
            </p>
        </div>

        <div>
            <span class="relative z-0 inline-flex shadow-sm rounded-md">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-l-md leading-5">
                        &laquo; Anterior
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                        &laquo; Anterior
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-5">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-blue-600 border border-blue-600 cursor-default leading-5">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                        Próximo &raquo;
                    </a>
                @else
                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-r-md leading-5">
                        Próximo &raquo;
                    </span>
                @endif
            </span>
        </div>
    </nav>
@endif
