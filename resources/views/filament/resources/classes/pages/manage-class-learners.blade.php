<x-filament-panels::page>
    <x-filament::section icon="heroicon-o-book-open" heading="Atur Peserta Didik - {{ $class->name }}"
        description="Drag & drop untuk mendaftarkan atau mengeluarkan peserta didik." class="mb-6">
        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 0.5rem;">
            <div>
                <label
                    style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Tahun
                    Ajaran</label>
                <select wire:model.live="academic_year_id"
                    style="display: block; width: 100%; border-radius: 0.5rem; border: 1px solid #d1d5db; padding: 0.5rem; font-size: 0.875rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); outline: none;">
                    <option value="">-- Pilih --</option>
                    @foreach (\App\Models\AcademicYear::where('is_archived', false)->get() as $ay)
                        <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label
                    style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Semester</label>
                <select wire:model.live="semester_id"
                    style="display: block; width: 100%; border-radius: 0.5rem; border: 1px solid #d1d5db; padding: 0.5rem; font-size: 0.875rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); outline: none;">
                    <option value="">-- Pilih --</option>
                    @foreach ($this->getSemesterOptions() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-filament::section>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;"
        x-data="sortableLists()" x-init="initSortable">

        {{-- LEFT: TERDAFTAR --}}
        <div
            style="background: #fff; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); border: 1px solid #e5e7eb; display: flex; flex-direction: column;">
            <div
                style="padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon alias="heroicon-o-check-circle"
                        style="width: 1.25rem; height: 1.25rem; color: #16a34a;" />
                    <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0;">Terdaftar di Kelas</h3>
                </div>
                <span
                    style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: #dcfce7; color: #15803d;">
                    {{ $this->getClassCount() }} / {{ $classCapacity }}
                </span>
            </div>

            {{-- Capacity Bar --}}
            <div style="padding: 0.5rem 1.25rem; border-bottom: 1px solid #f3f4f6;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div
                        style="flex: 1; height: 0.5rem; background-color: #e5e7eb; border-radius: 9999px; overflow: hidden;">
                        @php $capPct = min($this->getCapacityPercent(), 100); @endphp
                        <div
                            style="height: 100%; width: {{ $capPct }}%; border-radius: 9999px; transition: width 0.3s; background-color:
                                @if($capPct >= 100) #ef4444
                                @elseif($capPct >= 80) #f59e0b
                                @else #22c55e
                                @endif;">
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; color: #6b7280; white-space: nowrap;">
                        @php $displayCapPct = $this->getCapacityPercent(); @endphp
                        {{ $displayCapPct }}%
                    </span>
                </div>
            </div>

            {{-- Search --}}
            <div style="padding: 0.5rem 1.25rem; border-bottom: 1px solid #f3f4f6;">
                <div style="position: relative;">
                    <x-filament::icon alias="heroicon-o-magnifying-glass"
                        style="position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: #9ca3af;" />
                    <input wire:model.live.debounce.300ms="searchRegistered" type="text" placeholder="Cari terdaftar..."
                        style="display: block; width: 100%; padding: 0.5rem 0.5rem 0.5rem 2rem; border-radius: 0.375rem; border: 1px solid #d1d5db; font-size: 0.8rem; outline: none; box-sizing: border-box;">
                </div>
            </div>

            {{-- Bulk actions --}}
            @if(count($selectedRegistered) > 0)
                <div
                    style="padding: 0.5rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 0.5rem; background: #fef2f2;">
                    <span style="font-size: 0.75rem; color: #991b1b; font-weight: 500;">{{ count($selectedRegistered) }}
                        terpilih</span>
                    <button wire:click="removeMultipleFromClass" wire:confirm="Keluarkan {{ count($selectedRegistered) }} peserta terpilih?"
                        style="margin-left: auto; font-size: 0.75rem; padding: 0.25rem 0.625rem; border-radius: 0.375rem; border: 1px solid #fca5a5; background: #fff; color: #dc2626; cursor: pointer;">
                        Keluarkan Terpilih
                    </button>
                    <button wire:click="clearClass"
                        wire:confirm="Kosongkan semua peserta dari kelas ini (kecuali yang sudah memiliki nilai)?"
                        style="font-size: 0.75rem; padding: 0.25rem 0.625rem; border-radius: 0.375rem; border: 1px solid #fca5a5; background: #fff; color: #dc2626; cursor: pointer;">
                        Kosongkan Kelas
                    </button>
                </div>
            @endif

            <div wire:key="registered-{{ $class->id }}" x-ref="registeredList"
                style="padding: 0.75rem; min-height: 420px; max-height: 600px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.375rem; background-color: #f8fafc; border-bottom-left-radius: 0.75rem; border-bottom-right-radius: 0.75rem;">
                {{-- Select All --}}
                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem;">
                    <label class="sortable-ignore" style="display: flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; color: #6b7280; cursor: pointer;">
                        <input type="checkbox" class="sortable-ignore" wire:click="selectAllRegistered"
                            @if(count($selectedRegistered) === count($registeredLearners) && count($registeredLearners) > 0) checked @endif
                            style="accent-color: #2563eb;">
                        Pilih Semua
                    </label>
                </div>
                @forelse($registeredLearners as $item)
                    @php $isLocked = in_array($item->learner_id, $lockedLearnerIds); @endphp
                    <div wire:key="cl-{{ $item->id }}" data-clid="{{ $item->id }}"
                        data-lid="{{ $item->learner_id }}"
                        style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background-color: {{ $isLocked ? '#fef2f2' : '#eff6ff' }}; border: 1px solid {{ $isLocked ? '#fecaca' : '#bfdbfe' }}; border-radius: 0.5rem; cursor: {{ $isLocked ? 'default' : 'grab' }}; opacity: {{ $isLocked ? '0.8' : '1' }};">
                        <input type="checkbox" class="sortable-ignore" wire:model.live="selectedRegistered" value="{{ $item->id }}"
                            style="flex-shrink: 0; accent-color: #2563eb;">
                        <x-filament::icon alias="heroicon-o-grip-vertical"
                            style="width: 1rem; height: 1rem; color: {{ $isLocked ? '#fca5a5' : '#60a5fa' }}; flex-shrink: 0;" />
                        <div style="flex: 1; overflow: hidden;">
                            <div style="display: flex; align-items: center; gap: 0.375rem;">
                                <p
                                    style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $item->learner->name }}
                                </p>
                                @if($isLocked)
                                    <x-filament::icon alias="heroicon-o-lock-closed"
                                        style="width: 0.875rem; height: 0.875rem; color: #ef4444; flex-shrink: 0;" title="Memiliki data nilai" />
                                @endif
                            </div>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">NIS: {{ $item->learner->nis }}</p>
                        </div>
                        <span
                            style="flex-shrink: 0; font-size: 0.65rem; color: #9ca3af; text-align: right; line-height: 1.2;">
                            {{ $item->semester?->name }}
                        </span>
                        @if(!$isLocked)
                            <button class="sortable-ignore" wire:click="removeFromClass({{ $item->id }})"
                                wire:confirm="Keluarkan {{ $item->learner->name }} dari kelas?"
                                style="flex-shrink: 0; border: none; background: none; padding: 0.125rem; cursor: pointer; color: #ef4444;"
                                title="Keluarkan">
                                <x-filament::icon alias="heroicon-o-x-mark"
                                    style="width: 1rem; height: 1rem;" />
                            </button>
                        @endif
                    </div>
                @empty
                    <div wire:key="empty-registered"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 16rem; color: #9ca3af;">
                        <x-filament::icon alias="heroicon-o-users"
                            style="width: 2.5rem; height: 2.5rem; margin-bottom: 0.5rem;" />
                        <p style="font-size: 0.875rem; margin: 0;">Belum ada peserta didik</p>
                        <p style="font-size: 0.75rem; margin: 0.25rem 0 0;">Seret dari panel kanan atau gunakan tombol
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT: SEMUA PESERTA --}}
        <div
            style="background: #fff; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); border: 1px solid #e5e7eb; display: flex; flex-direction: column;">
            <div
                style="padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon alias="heroicon-o-users"
                        style="width: 1.25rem; height: 1.25rem; color: #6b7280;" />
                    <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0;">Semua Peserta Didik</h3>
                </div>
                <span
                    style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: #f3f4f6; color: #4b5563;">
                    {{ $unregisteredLearners->count() }}
                </span>
            </div>

            {{-- Filters --}}
            <div
                style="padding: 0.5rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <select wire:model.live="filterProgram"
                    style="width: 100%; border-radius: 0.375rem; border: 1px solid #d1d5db; padding: 0.375rem 0.5rem; font-size: 0.8rem; outline: none;">
                    <option value="">Semua Program</option>
                    @foreach ($this->getProgramOptions() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterGender"
                    style="width: 100%; border-radius: 0.375rem; border: 1px solid #d1d5db; padding: 0.375rem 0.5rem; font-size: 0.8rem; outline: none;">
                    <option value="">Semua Gender</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            {{-- Search --}}
            <div style="padding: 0.5rem 1.25rem; border-bottom: 1px solid #f3f4f6;">
                <div style="position: relative;">
                    <x-filament::icon alias="heroicon-o-magnifying-glass"
                        style="position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: #9ca3af;" />
                    <input wire:model.live.debounce.300ms="searchAvailable" type="text" placeholder="Cari nama atau NIS..."
                        style="display: block; width: 100%; padding: 0.5rem 0.5rem 0.5rem 2rem; border-radius: 0.375rem; border: 1px solid #d1d5db; font-size: 0.8rem; outline: none; box-sizing: border-box;">
                </div>
            </div>

            {{-- Bulk actions --}}
            @if(count($selectedAvailable) > 0)
                <div
                    style="padding: 0.5rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 0.5rem; background: #f0fdf4;">
                    <span style="font-size: 0.75rem; color: #166534; font-weight: 500;">{{ count($selectedAvailable) }}
                        terpilih</span>
                    <button wire:click="addMultipleToClass"
                        style="margin-left: auto; font-size: 0.75rem; padding: 0.25rem 0.625rem; border-radius: 0.375rem; border: 1px solid #86efac; background: #fff; color: #16a34a; cursor: pointer;">
                        Pindahkan Terpilih
                    </button>
                </div>
            @endif

            <div wire:key="available-{{ $class->id }}" x-ref="availableList"
                style="padding: 0.75rem; min-height: 420px; max-height: 600px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.375rem; background-color: #f8fafc; border-bottom-left-radius: 0.75rem; border-bottom-right-radius: 0.75rem;">
                {{-- Select All --}}
                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem;">
                    <label class="sortable-ignore" style="display: flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; color: #6b7280; cursor: pointer;">
                        <input type="checkbox" class="sortable-ignore" wire:click="selectAllAvailable"
                            @if(count($selectedAvailable) === count($unregisteredLearners) && count($unregisteredLearners) > 0) checked @endif
                            style="accent-color: #2563eb;">
                        Pilih Semua
                    </label>
                    <button class="sortable-ignore" wire:click="moveAllFiltered"
                        wire:confirm="Pindahkan semua peserta yang cocok dengan filter ke kelas ini?"
                        style="margin-left: auto; font-size: 0.75rem; padding: 0.25rem 0.625rem; border-radius: 0.375rem; border: 1px solid #d1d5db; background: #fff; color: #374151; cursor: pointer;">
                        Pindahkan Semua Filter
                    </button>
                </div>
                @forelse($unregisteredLearners as $learner)
                    @php $prevClass = $previousClassMap[$learner->id] ?? '-'; @endphp
                    <div wire:key="l-{{ $learner->id }}" data-lid="{{ $learner->id }}"
                        style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.5rem; cursor: grab;">
                        <input type="checkbox" class="sortable-ignore" wire:model.live="selectedAvailable" value="{{ $learner->id }}"
                            style="flex-shrink: 0; accent-color: #2563eb;">
                        <x-filament::icon alias="heroicon-o-grip-vertical"
                            style="width: 1rem; height: 1rem; color: #9ca3af; flex-shrink: 0;" />
                        <div style="flex: 1; overflow: hidden;">
                            <p
                                style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $learner->name }}
                            </p>
                            <div style="display: flex; align-items: center; gap: 0.375rem; flex-wrap: wrap; margin-top: 0.125rem;">
                                <span style="font-size: 0.7rem; color: #6b7280;">NIS: {{ $learner->nis }}</span>
                                <span
                                    style="font-size: 0.65rem; padding: 0.063rem 0.375rem; border-radius: 9999px; background: #e0e7ff; color: #4338ca;">
                                    {{ $learner->gender === 'L' ? 'L' : 'P' }}
                                </span>
                                @if($learner->program)
                                    <span
                                        style="font-size: 0.65rem; padding: 0.063rem 0.375rem; border-radius: 9999px; background: #dbeafe; color: #1d4ed8;">
                                        {{ $learner->program->code ?? $learner->program->name }}
                                    </span>
                                @endif
                                @if($prevClass !== '-')
                                    <span
                                        style="font-size: 0.65rem; padding: 0.063rem 0.375rem; border-radius: 9999px; background: #f3f4f6; color: #6b7280;">
                                        Sebelumnya: {{ str($prevClass)->limit(20) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div wire:key="empty-available"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 16rem; color: #9ca3af;">
                        <x-filament::icon alias="heroicon-o-check-circle"
                            style="width: 2.5rem; height: 2.5rem; margin-bottom: 0.5rem;" />
                        <p style="font-size: 0.875rem; margin: 0;">Semua peserta sudah terdaftar</p>
                        <p style="font-size: 0.75rem; margin: 0.25rem 0 0;">Atur ulang filter untuk mencari peserta lain
                        </p>
                    </div>
                @endforelse

                {{-- Load More --}}
                @if($hasMoreAvailable)
                    <div class="sortable-ignore" style="padding: 0.5rem; text-align: center;">
                        <button class="sortable-ignore" wire:click="loadMore" wire:loading.attr="disabled"
                            style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #d1d5db; background: #fff; color: #374151; font-size: 0.8rem; cursor: pointer;">
                            <span wire:loading.remove>Muat Lebih Banyak</span>
                            <span wire:loading>Memuat...</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            function sortableLists() {
                return {
                    registeredSortable: null,
                    availableSortable: null,
                    isLoading: false,

                    initSortable() {
                        this.destroySortable();
                        const self = this;
                        const options = {
                            group: {
                                name: 'learners',
                                pull: true,
                                put: true,
                            },
                            animation: 200,
                            filter: '.sortable-ignore',
                            onEnd(evt) {
                                if (self.isLoading) return;
                                if (evt.from === evt.to) return;

                                const clid = evt.item.dataset.clid;
                                const lid = evt.item.dataset.lid;

                                if (!clid && !lid) return;
                                self.isLoading = true;

                                if (clid) {
                                    @this.removeFromClass(parseInt(clid)).then(() => {
                                        self.isLoading = false;
                                    }).catch(() => {
                                        self.isLoading = false;
                                    });
                                } else if (lid) {
                                    @this.addToClass(parseInt(lid)).then(() => {
                                        self.isLoading = false;
                                    }).catch(() => {
                                        self.isLoading = false;
                                    });
                                }
                            },
                        };

                        const rl = this.$refs.registeredList;
                        const al = this.$refs.availableList;

                        if (rl) {
                            this.registeredSortable = new Sortable(rl, options);
                        }
                        if (al) {
                            this.availableSortable = new Sortable(al, options);
                        }
                    },

                    destroySortable() {
                        if (this.registeredSortable) {
                            this.registeredSortable.destroy();
                            this.registeredSortable = null;
                        }
                        if (this.availableSortable) {
                            this.availableSortable.destroy();
                            this.availableSortable = null;
                        }
                    },
                };
            }

            document.addEventListener('livewire:init', () => {
                Livewire.hook('morphed', () => {
                    setTimeout(() => {
                        document.querySelectorAll('[x-data="sortableLists()"]').forEach(el => {
                            if (typeof el.__x !== 'undefined') {
                                el.__x.$data.destroySortable();
                                el.__x.$data.initSortable();
                            }
                        });
                    }, 0);
                });
            });
        </script>
    @endpush
</x-filament-panels::page>
