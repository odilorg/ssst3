@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination Navigation">
        <div class="pagination__info">
            <p class="pagination__text">
                Showing <span class="pagination__highlight">{{ $paginator->firstItem() }}</span>
                to <span class="pagination__highlight">{{ $paginator->lastItem() }}</span>
                of <span class="pagination__highlight">{{ $paginator->total() }}</span> tours
            </p>
        </div>

        <ul class="pagination__list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="pagination__item pagination__item--disabled" aria-disabled="true">
                    <span class="pagination__link">
                        <i class="fas fa-chevron-left"></i>
                        <span class="pagination__link-text">Previous</span>
                    </span>
                </li>
            @else
                <li class="pagination__item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination__link" rel="prev">
                        <i class="fas fa-chevron-left"></i>
                        <span class="pagination__link-text">Previous</span>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="pagination__item pagination__item--disabled" aria-disabled="true">
                        <span class="pagination__link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination__item pagination__item--active" aria-current="page">
                                <span class="pagination__link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination__item">
                                <a href="{{ $url }}" class="pagination__link">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="pagination__item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination__link" rel="next">
                        <span class="pagination__link-text">Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="pagination__item pagination__item--disabled" aria-disabled="true">
                    <span class="pagination__link">
                        <span class="pagination__link-text">Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    <style>
        .pagination {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            margin: 3rem 0;
            padding: 2rem 0;
        }

        .pagination__info {
            text-align: center;
        }

        .pagination__text {
            font-size: 0.9375rem;
            color: #666;
            margin: 0;
        }

        .pagination__highlight {
            font-weight: 600;
            color: #1a5490;
        }

        .pagination__list {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            list-style: none;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        .pagination__item {
            display: inline-block;
        }

        .pagination__link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #374151;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            min-width: 44px;
            justify-content: center;
        }

        .pagination__link:hover {
            background: #f9fafb;
            border-color: #1a5490;
            color: #1a5490;
        }

        .pagination__link i {
            font-size: 0.75rem;
        }

        .pagination__item--active .pagination__link {
            background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
            color: white;
            border-color: #1a5490;
            font-weight: 600;
        }

        .pagination__item--active .pagination__link:hover {
            background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
            color: white;
        }

        .pagination__item--disabled .pagination__link {
            color: #d1d5db;
            cursor: not-allowed;
            background: #f9fafb;
            border-color: #e5e7eb;
        }

        .pagination__item--disabled .pagination__link:hover {
            background: #f9fafb;
            border-color: #e5e7eb;
            color: #d1d5db;
        }

        /* Mobile Responsive */
        @media (max-width: 640px) {
            .pagination {
                gap: 1rem;
                margin: 2rem 0;
                padding: 1.5rem 0;
            }

            .pagination__link-text {
                display: none;
            }

            .pagination__link {
                padding: 0.5rem 0.75rem;
                min-width: 40px;
            }

            .pagination__item:first-child .pagination__link-text,
            .pagination__item:last-child .pagination__link-text {
                display: inline;
            }

            .pagination__text {
                font-size: 0.875rem;
            }

            /* Show only first, last, current, and adjacent pages on mobile */
            .pagination__item:not(.pagination__item--active):not(:first-child):not(:last-child):not(.pagination__item--disabled) {
                display: none;
            }

            .pagination__item--active + .pagination__item,
            .pagination__item--active - .pagination__item {
                display: inline-block;
            }
        }

        /* Empty state styling */
        .empty-state {
            text-align: center;
            padding: 3rem;
            grid-column: 1/-1;
            color: #666;
        }

        .empty-state i {
            color: #ccc;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            margin: 0.5rem 0;
            color: #374151;
        }

        .empty-state p {
            margin: 0;
        }
    </style>
@endif
