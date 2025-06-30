<div class="fi-ta-text-item">
    @php
        $statusData = $getState();
    @endphp

    @if (is_array($statusData))
        <div class="flex flex-col gap-1.5">
            <!-- Badge principal con estilos de Filament -->
            <div class="flex justify-center">
                <div
                    class=" flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium 
                    @switch($statusData['status'])
                        @case('vencido')
                            fi-color-danger bg-danger-50 text-danger-600 ring-danger-600/10 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30
                            @break
                        @case('critico')
                            fi-color-warning bg-warning-50 text-warning-600 ring-warning-600/10 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30
                            @break
                        @case('por-vencer')
                            fi-color-warning bg-warning-50 text-warning-600 ring-warning-600/10 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30
                            @break
                        @case('proximo')
                            fi-color-info bg-info-50 text-info-600 ring-info-600/10 dark:bg-info-400/10 dark:text-info-400 dark:ring-info-400/30
                            @break
                        @case('vigente')
                            fi-color-success bg-success-50 text-success-600 ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30
                            @break
                        @default
                            fi-color-gray bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30
                    @endswitch
                ">
                    @switch($statusData['status'])
                        @case('vencido')
                            <x-heroicon-m-x-circle class="fi-badge-icon h-3.5 w-3.5" />
                        @break

                        @case('critico')
                            <x-heroicon-m-exclamation-triangle class="fi-badge-icon h-3.5 w-3.5" />
                        @break

                        @case('por-vencer')
                            <x-heroicon-m-clock class="fi-badge-icon h-3.5 w-3.5" />
                        @break

                        @case('proximo')
                            <x-heroicon-m-calendar-days class="fi-badge-icon h-3.5 w-3.5" />
                        @break

                        @case('vigente')
                            <x-heroicon-m-check-circle class="fi-badge-icon h-3.5 w-3.5" />
                        @break

                        @default
                            @if (isset($statusData['icon']))
                                <x-dynamic-component :component="'heroicon-m-' . $statusData['icon']" class="fi-badge-icon h-3.5 w-3.5" />
                            @else
                                <x-heroicon-m-information-circle class="fi-badge-icon h-3.5 w-3.5" />
                            @endif
                    @endswitch

                    <span class="fi-badge-text">{{ $statusData['label'] }}</span>
                </div>
            </div>
        </div>
    @else
        <!-- Estado simple con estilos de Filament -->
        <div
            class="fi-badge flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
            fi-color-gray bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30">
            <x-heroicon-m-question-mark-circle class="fi-badge-icon h-3.5 w-3.5" />
            <span class="fi-badge-text">{{ $statusData ?? 'Sin estado' }}</span>
        </div>
    @endif
</div>