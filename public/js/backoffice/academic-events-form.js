/**
 * 학술행사 등록/수정: 탭 전환, 온·오프라인 토글, 동적 행, 회원/초록/스폰서 모달, 연자 약력 모달, CKEditor 동기화
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bo-academic-event-form');
    if (!form) {
        return;
    }

    const searchSponsorsUrl = form.dataset.searchSponsorsUrl || '';
    const storeSponsorMasterUrl = form.dataset.storeSponsorMasterUrl || '';
    const searchAbstractsUrl = form.dataset.searchAbstractsUrl || '';
    const academicEventIdForAbstracts = form.dataset.academicEventId || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    document.getElementById('bo-academic-event-address-search')?.addEventListener('click', () => {
        if (typeof daum === 'undefined' || !daum.Postcode) {
            window.alert('주소 검색 스크립트를 불러오지 못했습니다.');
            return;
        }
        new daum.Postcode({
            oncomplete(data) {
                const base = document.getElementById('bo-academic-event-address');
                const detail = document.getElementById('bo-academic-event-address-detail');
                if (base) {
                    base.value = data.roadAddress || data.address || '';
                }
                if (detail) {
                    detail.focus();
                }
            },
        }).open();
    });

    const activeTabInput = document.getElementById('bo-academic-event-active-tab');
    const tabButtons = document.querySelectorAll('.js-academic-tab-btn');
    const tabPanels = document.querySelectorAll('.js-academic-tab-panel');
    const setActiveTab = (tabId) => {
        if (!activeTabInput) {
            return;
        }
        activeTabInput.value = tabId;
        tabButtons.forEach((button) => {
            button.classList.toggle('active', button.dataset.tab === tabId);
        });
        tabPanels.forEach((panel) => {
            panel.classList.toggle('bo-hidden', panel.dataset.tabPanel !== tabId);
        });
    };
    tabButtons.forEach((button) => {
        button.addEventListener('click', () => setActiveTab(button.dataset.tab));
    });
    if (activeTabInput?.value) {
        setActiveTab(activeTabInput.value);
    }

    document.querySelectorAll('.js-academic-session-delete-form').forEach((deleteForm) => {
        deleteForm.addEventListener('submit', (event) => {
            if (!window.confirm('정말 이 세션을 삭제하시겠습니까?')) {
                event.preventDefault();
            }
        });
    });
    document.querySelectorAll('.js-academic-session-delete-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const deleteFormId = button.dataset.deleteFormId || '';
            const deleteForm = deleteFormId ? document.getElementById(deleteFormId) : null;
            if (!deleteForm || !window.confirm('정말 이 세션을 삭제하시겠습니까?')) {
                return;
            }
            deleteForm.submit();
        });
    });

    document.querySelector('[data-remove-existing-target="event_material"]')?.addEventListener('click', () => {
        const del = document.getElementById('delete_event_material');
        if (del) {
            del.checked = true;
        }
        document.getElementById('bo-academic-event-material-existing-item')?.classList.add('bo-hidden');
    });

    document.querySelector('[data-remove-existing-target="abstract_book"]')?.addEventListener('click', () => {
        const del = document.getElementById('delete_abstract_book');
        if (del) {
            del.checked = true;
        }
        document.getElementById('bo-academic-abstract-book-existing-item')?.classList.add('bo-hidden');
    });

    const applyEventType = () => {
        const online = form.querySelector('.bo-event-type-input[value="online"]')?.checked;
        form.querySelectorAll('.bo-online-only').forEach((el) => {
            el.classList.toggle('bo-hidden', !online);
        });
        form.querySelectorAll('.bo-offline-only').forEach((el) => {
            el.classList.toggle('bo-hidden', !!online);
        });
    };
    form.querySelectorAll('.bo-event-type-input').forEach((r) => r.addEventListener('change', applyEventType));
    applyEventType();

    const nextIndex = (tbody) => tbody.querySelectorAll('tr.bo-repeat-row').length;

    const reindexAbstractFieldRows = () => {
        const tbody = document.getElementById('bo-abstract-fields-body');
        if (!tbody) {
            return;
        }
        const rows = tbody.querySelectorAll('tr.bo-repeat-row');
        rows.forEach((tr, i) => {
            tr.querySelectorAll('input[name^="abstract_fields["]').forEach((el) => {
                const n = el.getAttribute('name');
                if (n) {
                    el.setAttribute('name', n.replace(/^abstract_fields\[\d+\]/, `abstract_fields[${i}]`));
                }
            });
            const hiddenSort = tr.querySelector('input[name$="[sort_order]"]');
            if (hiddenSort) {
                hiddenSort.value = String(i + 1);
            }
        });
    };

    const reindexMainSponsorSlotRows = () => {
        const tbody = document.getElementById('bo-main-slots-body');
        if (!tbody) {
            return;
        }
        const rows = tbody.querySelectorAll('tr.bo-repeat-row');
        rows.forEach((tr, i) => {
            tr.querySelectorAll('input[name^="main_sponsor_slots["], select[name^="main_sponsor_slots["]').forEach((el) => {
                const n = el.getAttribute('name');
                if (n) {
                    el.setAttribute('name', n.replace(/^main_sponsor_slots\[\d+\]/, `main_sponsor_slots[${i}]`));
                }
            });
            const hiddenSort = tr.querySelector('input[name$="[sort_order]"]');
            if (hiddenSort) {
                hiddenSort.value = String(i + 1);
            }
        });
    };

    const reindexSponsorRows = () => {
        const tbody = document.getElementById('bo-sponsors-body');
        if (!tbody) {
            return;
        }
        const rows = tbody.querySelectorAll('tr.bo-sponsor-row');
        rows.forEach((tr, i) => {
            tr.querySelectorAll('input[name^="sponsors["], select[name^="sponsors["]').forEach((el) => {
                const n = el.getAttribute('name');
                if (n) {
                    el.setAttribute('name', n.replace(/^sponsors\[\d+\]/, `sponsors[${i}]`));
                }
            });
            tr.querySelectorAll('input[name^="sponsor_logos["]').forEach((el) => {
                const n = el.getAttribute('name');
                if (n) {
                    el.setAttribute('name', n.replace(/^sponsor_logos\[\d+\]/, `sponsor_logos[${i}]`));
                }
            });
            const hiddenSort = tr.querySelector('input[name$="[sort_order]"]');
            if (hiddenSort) {
                hiddenSort.value = String(i + 1);
            }
        });
    };

    const reindexSpeakerRows = () => {
        const tbody = document.getElementById('bo-speakers-body');
        if (!tbody) {
            return;
        }
        const rows = tbody.querySelectorAll('tr.bo-speaker-row');
        rows.forEach((tr, i) => {
            tr.querySelectorAll('input[name^="speakers["], textarea[name^="speakers["], select[name^="speakers["]').forEach((el) => {
                const n = el.getAttribute('name');
                if (n) {
                    el.setAttribute('name', n.replace(/^speakers\[\d+\]/, `speakers[${i}]`));
                }
            });
            tr.querySelectorAll('input[name^="speaker_images["]').forEach((el) => {
                const n = el.getAttribute('name');
                if (n) {
                    el.setAttribute('name', n.replace(/^speaker_images\[\d+\]/, `speaker_images[${i}]`));
                }
            });
            const hiddenSort = tr.querySelector('input[name$="[sort_order]"]');
            if (hiddenSort) {
                hiddenSort.value = String(i + 1);
            }
        });
    };

    const abstractFieldsBody = document.getElementById('bo-abstract-fields-body');
    if (abstractFieldsBody && typeof Sortable !== 'undefined') {
        new Sortable(abstractFieldsBody, {
            handle: '.sort-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onStart: (evt) => {
                evt.item.classList.add('dragging');
            },
            onEnd: (evt) => {
                evt.item.classList.remove('dragging');
                reindexAbstractFieldRows();
            },
        });
    }

    const mainSlotsBody = document.getElementById('bo-main-slots-body');
    const mainSlotsTable = document.getElementById('bo-main-slots-table');
    let mainSponsorOptions = [];
    if (mainSlotsTable?.dataset.sponsorOptions) {
        try {
            mainSponsorOptions = JSON.parse(mainSlotsTable.dataset.sponsorOptions);
        } catch (e) {
            mainSponsorOptions = [];
        }
    }
    const ensureMainSlotsEmptyRow = () => {
        if (!mainSlotsBody) {
            return;
        }
        if (mainSlotsBody.querySelector('tr.bo-repeat-row')) {
            mainSlotsBody.querySelector('.bo-main-slots-empty-row')?.closest('tr')?.remove();
            return;
        }
        if (!mainSlotsBody.querySelector('.bo-main-slots-empty-row')) {
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 4;
            td.className = 'text-center text-muted bo-main-slots-empty-row';
            td.textContent = '노출 스폰서가 없습니다.';
            tr.appendChild(td);
            mainSlotsBody.appendChild(tr);
        }
    };
    const selectedMainSponsorIndexes = () => Array.from(mainSlotsBody?.querySelectorAll('.bo-main-sponsor-select') || [])
        .map((select) => String(select.value));
    const refreshMainSponsorOptions = () => {
        const sponsorRows = Array.from(document.querySelectorAll('#bo-sponsors-body tr.bo-sponsor-row'));
        mainSponsorOptions = sponsorRows
            .map((row, index) => {
                const name = (row.querySelector('.bo-sponsor-name')?.value || '').trim();
                if (name === '') {
                    return null;
                }
                const levelSelect = row.querySelector('select[name^="sponsors["][name$="[level]"]');
                const level = levelSelect?.value || 'exhibitors';
                const levelLabel = levelSelect?.selectedOptions?.[0]?.textContent || level;

                return {
                    sponsor_index: index,
                    name,
                    placement: level,
                    placement_label: levelLabel,
                };
            })
            .filter(Boolean);

        if (mainSlotsTable) {
            mainSlotsTable.dataset.sponsorOptions = JSON.stringify(mainSponsorOptions);
        }
    };
    const rebuildMainSponsorSelect = (select, selectedSponsorIndex) => {
        if (!select) {
            return;
        }
        const selectedValue = selectedSponsorIndex !== undefined ? String(selectedSponsorIndex) : String(select.value || '');
        select.innerHTML = '';
        mainSponsorOptions.forEach((option) => {
            const opt = document.createElement('option');
            opt.value = String(option.sponsor_index);
            opt.textContent = option.name;
            opt.dataset.placement = option.placement;
            opt.dataset.placementLabel = option.placement_label;
            if (String(option.sponsor_index) === selectedValue) {
                opt.selected = true;
            }
            select.appendChild(opt);
        });
    };
    const rebuildAllMainSponsorSelects = () => {
        refreshMainSponsorOptions();
        mainSlotsBody?.querySelectorAll('.bo-main-sponsor-select').forEach((select) => {
            rebuildMainSponsorSelect(select);
            const row = select.closest('tr');
            if (row) {
                syncMainSponsorPlacement(row);
            }
        });
    };
    const syncMainSponsorPlacement = (tr) => {
        const select = tr.querySelector('.bo-main-sponsor-select');
        const option = select?.selectedOptions?.[0];
        const cell = tr.querySelector('.bo-main-sponsor-placement-cell');
        const hidden = cell?.querySelector('input[name$="[placement]"]');
        if (!option || !cell || !hidden) {
            if (cell && hidden) {
                hidden.value = '';
                cell.childNodes.forEach((node) => {
                    if (node.nodeType === Node.TEXT_NODE) {
                        node.remove();
                    }
                });
                cell.insertBefore(document.createTextNode('- '), hidden);
            }
            return;
        }
        const label = option.dataset.placementLabel || '';
        hidden.value = option.dataset.placement || '';
        cell.childNodes.forEach((node) => {
            if (node.nodeType === Node.TEXT_NODE) {
                node.remove();
            }
        });
        cell.insertBefore(document.createTextNode(label + ' '), hidden);
    };
    const addMainSponsorSlotRow = (selectedSponsorIndex) => {
        if (!mainSlotsBody) {
            return;
        }
        const i = nextIndex(mainSlotsBody);
        const tr = document.createElement('tr');
        tr.className = 'bo-repeat-row';

        const sponsorCell = document.createElement('td');
        const inactiveInput = document.createElement('input');
        inactiveInput.type = 'hidden';
        inactiveInput.name = `main_sponsor_slots[${i}][active]`;
        inactiveInput.value = '0';
        const activeInput = document.createElement('input');
        activeInput.type = 'hidden';
        activeInput.name = `main_sponsor_slots[${i}][active]`;
        activeInput.value = '1';
        const select = document.createElement('select');
        select.name = `main_sponsor_slots[${i}][sponsor_index]`;
        select.className = 'board-form-control bo-main-sponsor-select';
        rebuildMainSponsorSelect(select, selectedSponsorIndex);
        sponsorCell.appendChild(inactiveInput);
        sponsorCell.appendChild(activeInput);
        sponsorCell.appendChild(select);

        const placementCell = document.createElement('td');
        placementCell.className = 'bo-main-sponsor-placement-cell';
        const placementInput = document.createElement('input');
        placementInput.type = 'hidden';
        placementInput.name = `main_sponsor_slots[${i}][placement]`;
        placementCell.appendChild(placementInput);

        const sortCell = document.createElement('td');
        sortCell.className = 'text-center sort-handle-cell';
        const icon = document.createElement('i');
        icon.className = 'fas fa-grip-vertical sort-handle';
        icon.title = '드래그하여 순서 변경';
        const sortInput = document.createElement('input');
        sortInput.type = 'hidden';
        sortInput.name = `main_sponsor_slots[${i}][sort_order]`;
        sortInput.value = String(i + 1);
        sortCell.appendChild(icon);
        sortCell.appendChild(sortInput);

        const actionCell = document.createElement('td');
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-sm btn-secondary bo-remove-row-btn';
        removeButton.textContent = '삭제';
        actionCell.appendChild(removeButton);

        tr.appendChild(sponsorCell);
        tr.appendChild(placementCell);
        tr.appendChild(sortCell);
        tr.appendChild(actionCell);
        mainSlotsBody.querySelector('.bo-main-slots-empty-row')?.closest('tr')?.remove();
        mainSlotsBody.appendChild(tr);
        syncMainSponsorPlacement(tr);
        reindexMainSponsorSlotRows();
    };
    if (mainSlotsBody && typeof Sortable !== 'undefined') {
        new Sortable(mainSlotsBody, {
            handle: '.sort-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onStart: (evt) => {
                evt.item.classList.add('dragging');
            },
            onEnd: (evt) => {
                evt.item.classList.remove('dragging');
                reindexMainSponsorSlotRows();
            },
        });
    }
    document.getElementById('bo-add-main-slot-btn')?.addEventListener('click', () => {
        refreshMainSponsorOptions();
        const selectedIndexes = selectedMainSponsorIndexes();
        const option = mainSponsorOptions.find((item) => !selectedIndexes.includes(String(item.sponsor_index)));
        if (!option && mainSponsorOptions.length > 0) {
            window.alert('등록된 스폰서가 모두 추가되어 있습니다.');
            return;
        }
        addMainSponsorSlotRow(option?.sponsor_index ?? '');
    });
    mainSlotsBody?.addEventListener('change', (event) => {
        const select = event.target.closest('.bo-main-sponsor-select');
        if (!select) {
            return;
        }
        const tr = select.closest('tr');
        if (tr) {
            syncMainSponsorPlacement(tr);
        }
    });
    mainSlotsBody?.addEventListener('click', (event) => {
        if (!event.target.closest('.bo-remove-row-btn')) {
            return;
        }
        setTimeout(() => {
            reindexMainSponsorSlotRows();
            ensureMainSlotsEmptyRow();
        }, 0);
    });
    mainSlotsBody?.querySelectorAll('tr.bo-repeat-row').forEach(syncMainSponsorPlacement);
    ensureMainSlotsEmptyRow();

    document.getElementById('bo-add-venue-floor-btn')?.addEventListener('click', () => {
        const tbody = document.getElementById('bo-venue-floors-body');
        const tpl = document.getElementById('bo-template-venue-floor');
        if (!tbody || !tpl) {
            return;
        }
        const i = nextIndex(tbody);
        const tr = tpl.cloneNode(true);
        tr.removeAttribute('id');
        tr.classList.remove('bo-template');
        tr.classList.add('bo-repeat-row');
        tr.querySelectorAll('input, select').forEach((el) => {
            const n = el.getAttribute('name');
            if (n) {
                el.setAttribute('name', n.replace('__I__', String(i)));
            }
        });
        tbody.appendChild(tr);
    });

    document.getElementById('bo-add-abstract-field-btn')?.addEventListener('click', () => {
        const tbody = document.getElementById('bo-abstract-fields-body');
        const tpl = document.getElementById('bo-template-abstract-field');
        if (!tbody || !tpl) {
            return;
        }
        const i = nextIndex(tbody);
        const tr = tpl.cloneNode(true);
        tr.removeAttribute('id');
        tr.classList.remove('bo-template');
        tr.classList.add('bo-repeat-row');
        tr.querySelectorAll('input').forEach((el) => {
            const n = el.getAttribute('name');
            if (n) {
                el.setAttribute('name', n.replaceAll('__I__', String(i)));
            }
        });
        tbody.appendChild(tr);
        reindexAbstractFieldRows();
    });

    const bindRemove = (root) => {
        root.querySelectorAll('.bo-remove-row-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const tr = btn.closest('tr');
                if (tr && tr.parentNode) {
                    tr.parentNode.removeChild(tr);
                }
            });
        });
    };
    bindRemove(form);
    form.addEventListener('click', (e) => {
        const t = e.target;
        if (t && t.classList && t.classList.contains('bo-remove-row-btn')) {
            const tr = t.closest('tr');
            if (tr && tr.parentNode) {
                tr.parentNode.removeChild(tr);
            }
            bindRemove(form);
        }
    });

    form.addEventListener(
        'click',
        (e) => {
            const btn = e.target.closest('.bo-remove-row-btn');
            if (!btn) {
                return;
            }
            const tr = btn.closest('tr');
            if (!tr) {
                return;
            }
            const abstractBody = document.getElementById('bo-abstract-fields-body');
            const mainSlotsBodyEl = document.getElementById('bo-main-slots-body');
            const inAbstract = abstractBody?.contains(tr);
            const inMainSlots = mainSlotsBodyEl?.contains(tr);
            if (!inAbstract && !inMainSlots) {
                return;
            }
            queueMicrotask(() => {
                if (inAbstract) {
                    reindexAbstractFieldRows();
                }
                if (inMainSlots) {
                    reindexMainSponsorSlotRows();
                }
            });
        },
        true,
    );

    reindexAbstractFieldRows();
    reindexMainSponsorSlotRows();

    let speakerBioRow = null;
    const bioModal = document.getElementById('bo-speaker-bio-modal');
    const bioTextarea = document.getElementById('bo-speaker-bio-textarea');
    const bioTitle = document.getElementById('bo-speaker-bio-modal-title');

    const showBsModal = (el) => {
        if (!el) {
            return;
        }
        el.classList.remove('d-none');
        el.classList.add('show');
        if (el.classList.contains('bo-academic-modal')) {
            el.style.removeProperty('display');
        } else {
            el.style.display = 'block';
        }
        el.removeAttribute('aria-hidden');
    };

    const hideBsModal = (el) => {
        if (!el) {
            return;
        }
        el.classList.remove('show');
        if (el.classList.contains('bo-academic-modal')) {
            el.style.removeProperty('display');
        } else {
            el.style.display = 'none';
        }
        el.classList.add('d-none');
        el.setAttribute('aria-hidden', 'true');
    };

    document.querySelectorAll('.bo-modal-close').forEach((btn) => {
        btn.addEventListener('click', () => {
            hideBsModal(bioModal);
            hideBsModal(document.getElementById('bo-sponsor-modal'));
            const absM = document.getElementById('bo-abstract-search-modal');
            if (absM) {
                absM.style.display = 'none';
            }
        });
    });

    form.addEventListener('click', (e) => {
        const btn = e.target.closest('.bo-speaker-bio-btn');
        if (!btn) {
            return;
        }
        const tr = btn.closest('tr');
        speakerBioRow = tr;
        const nameInp = tr?.querySelector('.bo-speaker-name');
        const hidden = tr?.querySelector('.bo-speaker-bio');
        if (bioTitle) {
            bioTitle.textContent = '연자 약력 — ' + (nameInp?.value?.trim() || '(이름 미입력)');
        }
        if (bioTextarea && hidden) {
            bioTextarea.value = hidden.value || '';
        }
        showBsModal(bioModal);
    });

    document.getElementById('bo-speaker-bio-save-btn')?.addEventListener('click', () => {
        if (!speakerBioRow || !bioTextarea) {
            return;
        }
        const hidden = speakerBioRow.querySelector('.bo-speaker-bio');
        const button = speakerBioRow.querySelector('.bo-speaker-bio-btn');
        if (hidden) {
            hidden.value = bioTextarea.value;
        }
        if (button) {
            button.textContent = bioTextarea.value.trim() ? '약력 수정' : '약력 입력';
        }
        hideBsModal(bioModal);
    });

    const addSpeakerRow = (preset) => {
        const tbody = document.getElementById('bo-speakers-body');
        if (!tbody) {
            return;
        }
        const p = preset || { source: 'manual' };
        const i = tbody.querySelectorAll('tr.bo-repeat-row').length;
        const tr = document.createElement('tr');
        tr.className = 'bo-repeat-row bo-speaker-row';
        tr.innerHTML = `
            <td>
                <input type="hidden" name="speakers[${i}][source]" class="bo-speaker-source" value="manual">
                <input type="hidden" name="speakers[${i}][member_id]" class="bo-speaker-member-id" value="">
                <input type="hidden" name="speakers[${i}][academic_event_abstract_id]" class="bo-speaker-abstract-id" value="">
                <input type="hidden" name="speakers[${i}][sort_order]" value="${i + 1}">
                <input type="hidden" name="speakers[${i}][image_path]" value="">
                <input type="text" name="speakers[${i}][name]" class="board-form-control bo-speaker-name" value="">
            </td>
            <td><input type="text" name="speakers[${i}][affiliation]" class="board-form-control bo-speaker-affiliation" value=""></td>
            <td><input type="text" name="speakers[${i}][position]" class="board-form-control bo-speaker-position" value=""></td>
            <td><input type="file" name="speaker_images[${i}]" class="board-form-control" accept="image/*"></td>
            <td><input type="text" name="speakers[${i}][abstract_title]" class="board-form-control" value=""></td>
            <td>
                <input type="hidden" name="speakers[${i}][bio]" class="bo-speaker-bio" value="">
                <button type="button" class="btn btn-sm btn-outline-primary bo-speaker-bio-btn">약력 입력</button>
            </td>
            <td><button type="button" class="btn btn-sm btn-secondary bo-remove-row-btn">삭제</button></td>`;
        tr.querySelector('.bo-speaker-source').value = p.source || 'manual';
        tr.querySelector('.bo-speaker-member-id').value = p.member_id || '';
        tr.querySelector('.bo-speaker-abstract-id').value = p.academic_event_abstract_id || '';
        tr.querySelector('.bo-speaker-name').value = p.name || '';
        tr.querySelector('.bo-speaker-affiliation').value = p.affiliation || '';
        tr.querySelector('.bo-speaker-position').value = p.position || '';
        tr.querySelector('input[name$="[abstract_title]"]').value = p.abstract_title || '';
        tr.querySelector('.bo-speaker-bio').value = p.bio || '';
        if (p.bio && p.bio.trim()) {
            tr.querySelector('.bo-speaker-bio-btn').textContent = '약력 수정';
        }
        tbody.appendChild(tr);
        reindexSpeakerRows();
    };

    document.getElementById('bo-speaker-add-manual-btn')?.addEventListener('click', () => addSpeakerRow({ source: 'manual' }));

    const speakerMemberSelector = document.getElementById('bo-academic-speaker-member-selector');
    speakerMemberSelector?.addEventListener('bo-member-selected', (ev) => {
        const d = ev.detail || {};
        if (!d.id) {
            return;
        }
        addSpeakerRow({
            source: 'member',
            member_id: String(d.id),
            name: d.name || d.label || '',
            affiliation: d.organization || '',
            position: d.position || '',
        });
        bindRemove(form);
        const hidId = speakerMemberSelector.querySelector('.js-member-id');
        const hidLabel = speakerMemberSelector.querySelector('.js-member-label');
        if (hidId) {
            hidId.value = '';
        }
        if (hidLabel) {
            hidLabel.value = '';
        }
    });

    const abstractModalEl = document.getElementById('bo-abstract-search-modal');
    const abstractResultsBody = abstractModalEl?.querySelector('.js-abstract-results');
    const abstractPaginationRoot = abstractModalEl?.querySelector('.js-abstract-pagination');
    const abstractKeywordInput = abstractModalEl?.querySelector('.js-abstract-keyword');
    const abstractPresentationSelect = abstractModalEl?.querySelector('.js-abstract-presentation-type');
    const abstractStatusSelect = abstractModalEl?.querySelector('.js-abstract-status');
    let abstractCurrentPage = 1;

    const escAbs = (value) =>
        String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

    const cellAbs = (v) => {
        const s = v === null || v === undefined ? '' : String(v);
        if (s === '') {
            return '-';
        }
        return escAbs(s);
    };

    const closeAbstractModal = () => {
        if (abstractModalEl) {
            abstractModalEl.style.display = 'none';
        }
    };

    const openAbstractModal = () => {
        if (!abstractModalEl) {
            return;
        }
        if (!searchAbstractsUrl || !academicEventIdForAbstracts) {
            window.alert('행사를 먼저 저장한 뒤 초록 연동을 사용할 수 있습니다.');
            return;
        }
        abstractModalEl.style.display = 'block';
        abstractKeywordInput?.focus();
    };

    const isAbstractAlreadyLinked = (abstractId) => {
        const idStr = String(abstractId);
        const tbody = document.getElementById('bo-speakers-body');
        if (!tbody) {
            return false;
        }
        return Array.from(tbody.querySelectorAll('.bo-speaker-abstract-id')).some((inp) => String(inp.value) === idStr);
    };

    const renderAbstractPagination = (meta) => {
        if (!abstractPaginationRoot || !meta || (meta.last_page ?? 1) <= 1) {
            if (abstractPaginationRoot) {
                abstractPaginationRoot.innerHTML = '';
            }
            return;
        }
        const current = Number(meta.current_page ?? 1);
        const last = Number(meta.last_page ?? 1);
        const start = Math.max(1, current - 2);
        const end = Math.min(last, start + 4);
        const numberButtons = [];
        for (let page = start; page <= end; page += 1) {
            const activeClass = page === current ? 'active' : '';
            numberButtons.push(
                `<li class="page-item ${activeClass}"><a class="page-link js-abstract-page" data-page="${page}" href="#">${page}</a></li>`,
            );
        }
        abstractPaginationRoot.innerHTML = `
            <ul class="pagination">
                <li class="page-item ${current <= 1 ? 'disabled' : ''}">
                    <a class="page-link js-abstract-page" data-page="${Math.max(1, current - 1)}" href="#" aria-label="이전 페이지">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                ${numberButtons.join('')}
                <li class="page-item ${current >= last ? 'disabled' : ''}">
                    <a class="page-link js-abstract-page" data-page="${Math.min(last, current + 1)}" href="#" aria-label="다음 페이지">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>`;
    };

    const renderAbstractRows = (rows, meta) => {
        if (!abstractResultsBody) {
            return;
        }
        if (!Array.isArray(rows) || rows.length === 0) {
            abstractResultsBody.innerHTML = '<tr><td colspan="9" class="text-center">검색 결과가 없습니다.</td></tr>';
            return;
        }
        const startNo = ((Number(meta?.current_page ?? 1) - 1) * Number(meta?.per_page ?? 10)) + 1;
        abstractResultsBody.innerHTML = rows
            .map((row, idx) => {
                const indexNo = startNo + idx;
                const id = row.id;
                return `
                    <tr>
                        <td>${indexNo}</td>
                        <td>${cellAbs(row.title)}</td>
                        <td>${cellAbs(row.author_name)}</td>
                        <td>${cellAbs(row.registered_by_label ?? row.registered_by)}</td>
                        <td>${cellAbs(row.presentation_type_label ?? row.presentation_type)}</td>
                        <td>${cellAbs(row.submitted_at)}</td>
                        <td>${cellAbs(row.status_label ?? row.status)}</td>
                        <td>${cellAbs(row.file_receipt_label ?? row.file_receipt_status)}</td>
                        <td><button type="button" class="btn btn-sm btn-primary js-select-abstract"
                            data-id="${escAbs(id)}"
                            data-author-name="${escAbs(row.author_name ?? '')}"
                            data-title="${escAbs(row.title ?? '')}"
                            data-member-id="${escAbs(row.member_id ?? '')}"
                        >선택</button></td>
                    </tr>`;
            })
            .join('');
    };

    const fetchAbstracts = async (page = 1) => {
        if (!abstractResultsBody || !searchAbstractsUrl || !academicEventIdForAbstracts) {
            return;
        }
        abstractCurrentPage = page;
        const url = new URL(searchAbstractsUrl, window.location.origin);
        url.searchParams.set('academic_event_id', academicEventIdForAbstracts);
        url.searchParams.set('page', String(page));
        const perPage = '10';
        url.searchParams.set('per_page', perPage);
        if (abstractPresentationSelect?.value) {
            url.searchParams.set('presentation_type', abstractPresentationSelect.value);
        }
        if (abstractStatusSelect?.value) {
            url.searchParams.set('status', abstractStatusSelect.value);
        }
        const kw = (abstractKeywordInput?.value || '').trim();
        if (kw !== '') {
            url.searchParams.set('search_keyword', kw);
        }

        abstractResultsBody.innerHTML = '<tr><td colspan="9" class="text-center">조회 중입니다...</td></tr>';
        if (abstractPaginationRoot) {
            abstractPaginationRoot.innerHTML = '';
        }

        try {
            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (!response.ok) {
                throw new Error('검색 요청 실패');
            }
            const payload = await response.json();
            renderAbstractRows(payload.data ?? [], payload.meta ?? null);
            renderAbstractPagination(payload.meta ?? null);
        } catch {
            abstractResultsBody.innerHTML = '<tr><td colspan="9" class="text-center">조회 중 오류가 발생했습니다.</td></tr>';
            if (abstractPaginationRoot) {
                abstractPaginationRoot.innerHTML = '';
            }
        }
    };

    document.getElementById('bo-speaker-add-abstract-btn')?.addEventListener('click', () => {
        openAbstractModal();
        fetchAbstracts(1);
    });

    abstractModalEl?.querySelector('.js-close-abstract-modal')?.addEventListener('click', closeAbstractModal);
    abstractModalEl?.addEventListener('click', (event) => {
        if (event.target === abstractModalEl) {
            closeAbstractModal();
        }
    });
    abstractModalEl?.querySelector('.js-search-abstract')?.addEventListener('click', () => fetchAbstracts(1));
    abstractKeywordInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            fetchAbstracts(1);
        }
    });
    abstractPaginationRoot?.addEventListener('click', (event) => {
        const target = event.target instanceof HTMLElement ? event.target.closest('.js-abstract-page') : null;
        if (!(target instanceof HTMLElement)) {
            return;
        }
        event.preventDefault();
        if (target.closest('.disabled')) {
            return;
        }
        const page = Number(target.dataset.page ?? '1');
        if (Number.isNaN(page) || page < 1 || page === abstractCurrentPage) {
            return;
        }
        fetchAbstracts(page);
    });

    abstractResultsBody?.addEventListener('click', (event) => {
        const target = event.target.closest('.js-select-abstract');
        if (!(target instanceof HTMLElement) || !target.classList.contains('js-select-abstract')) {
            return;
        }
        const abstractId = target.dataset.id ?? '';
        if (!abstractId) {
            return;
        }
        if (isAbstractAlreadyLinked(abstractId)) {
            window.alert('이미 연자로 추가된 초록입니다.');
            return;
        }
        const authorName = target.getAttribute('data-author-name') || '';
        const title = target.getAttribute('data-title') || '';
        const memberId = target.getAttribute('data-member-id') || '';
        addSpeakerRow({
            source: 'abstract',
            academic_event_abstract_id: abstractId,
            name: authorName,
            abstract_title: title,
            member_id: memberId || '',
            affiliation: '',
            position: '',
        });
        bindRemove(form);
        closeAbstractModal();
    });

    const addSponsorRow = (preset) => {
        const tbody = document.getElementById('bo-sponsors-body');
        if (!tbody) {
            return null;
        }
        const p = preset || {};
        const filterEl = document.getElementById('bo-sponsor-level-filter');
        const filterValue = filterEl?.value || 'all';
        const defaultLevel = filterValue !== 'all' ? filterValue : 'exhibitors';
        const i = tbody.querySelectorAll('tr.bo-repeat-row').length;
        const tr = document.createElement('tr');
        tr.className = 'bo-repeat-row bo-sponsor-row';
        tr.innerHTML = `
            <td class="text-center sort-handle-cell">
                <i class="fas fa-grip-vertical sort-handle" title="드래그하여 순서 변경"></i>
            </td>
            <td>
                <input type="hidden" name="sponsors[${i}][academic_sponsor_master_id]" class="bo-sponsor-master-id" value="">
                <input type="hidden" name="sponsors[${i}][sort_order]" value="${i + 1}">
                <input type="hidden" name="sponsors[${i}][logo_path]" value="">
                <input type="text" name="sponsors[${i}][name]" class="board-form-control bo-sponsor-name" value="">
            </td>
            <td>
                <select name="sponsors[${i}][level]" class="board-form-control">
                    <option value="vip">VIP</option><option value="gold">Gold</option>
                    <option value="silver">Silver</option><option value="exhibitors" selected>Exhibitors</option>
                </select>
            </td>
            <td><input type="file" name="sponsor_logos[${i}]" class="board-form-control" accept="image/*"></td>
            <td><button type="button" class="btn btn-sm btn-secondary bo-remove-row-btn">삭제</button></td>`;
        tr.querySelector('.bo-sponsor-master-id').value = p.master_id || '';
        tr.querySelector('.bo-sponsor-name').value = p.name || '';
        const levelSelect = tr.querySelector('select[name^="sponsors["][name$="[level]"]');
        if (levelSelect) {
            levelSelect.value = p.level || defaultLevel;
        }
        tbody.appendChild(tr);
        reindexSponsorRows();
        applySponsorLevelFilter();
        rebuildAllMainSponsorSelects();

        return tr;
    };

    const applySponsorLevelFilter = () => {
        const filterEl = document.getElementById('bo-sponsor-level-filter');
        const tbody = document.getElementById('bo-sponsors-body');
        if (!filterEl || !tbody) {
            return;
        }
        const filterValue = filterEl.value || 'all';
        tbody.querySelectorAll('tr.bo-sponsor-row').forEach((row) => {
            const levelSelect = row.querySelector('select[name^="sponsors["][name$="[level]"]');
            const levelValue = levelSelect?.value || '';
            const visible = filterValue === 'all' || levelValue === filterValue;
            row.classList.toggle('bo-hidden', !visible);
        });
    };

    document.getElementById('bo-sponsor-add-manual-btn')?.addEventListener('click', () => addSponsorRow({}));

    const sponsorRowsBody = document.getElementById('bo-sponsors-body');
    if (sponsorRowsBody && typeof Sortable !== 'undefined') {
        new Sortable(sponsorRowsBody, {
            handle: '.sort-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onStart(evt) {
                evt.item.classList.add('dragging');
            },
            onEnd(evt) {
                evt.item.classList.remove('dragging');
                reindexSponsorRows();
                applySponsorLevelFilter();
                rebuildAllMainSponsorSelects();
            },
        });
    }

    const sponsorModalEl = document.getElementById('bo-sponsor-modal');
    const sponsorSearchKeywordInput = document.getElementById('bo-sponsor-search-keyword');
    const sponsorSearchTbody = document.getElementById('bo-sponsor-search-tbody');
    const sponsorSearchPagination = document.getElementById('bo-sponsor-search-pagination');
    let sponsorCurrentPage = 1;

    const escSponsor = (value) =>
        String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

    const renderSponsorSearchPagination = (meta) => {
        if (!sponsorSearchPagination || !meta || (meta.last_page ?? 1) <= 1) {
            if (sponsorSearchPagination) {
                sponsorSearchPagination.innerHTML = '';
            }
            return;
        }

        const current = Number(meta.current_page ?? 1);
        const last = Number(meta.last_page ?? 1);
        const start = Math.max(1, current - 2);
        const end = Math.min(last, start + 4);
        const numberButtons = [];
        for (let page = start; page <= end; page += 1) {
            const activeClass = page === current ? 'active' : '';
            numberButtons.push(
                `<li class="page-item ${activeClass}"><a class="page-link js-sponsor-page" data-page="${page}" href="#">${page}</a></li>`,
            );
        }

        sponsorSearchPagination.innerHTML = `
            <ul class="pagination">
                <li class="page-item ${current <= 1 ? 'disabled' : ''}">
                    <a class="page-link js-sponsor-page" data-page="${Math.max(1, current - 1)}" href="#" aria-label="이전 페이지">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                ${numberButtons.join('')}
                <li class="page-item ${current >= last ? 'disabled' : ''}">
                    <a class="page-link js-sponsor-page" data-page="${Math.min(last, current + 1)}" href="#" aria-label="다음 페이지">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>`;
    };

    const renderSponsorSearchRows = (rows) => {
        if (!sponsorSearchTbody) {
            return;
        }

        if (!Array.isArray(rows) || rows.length === 0) {
            sponsorSearchTbody.innerHTML = '<tr><td colspan="2" class="text-center">검색 결과가 없습니다.</td></tr>';
            return;
        }

        sponsorSearchTbody.innerHTML = rows
            .map((s) => {
                const pick = encodeURIComponent(JSON.stringify({ id: s.id, name: s.name }));
                return `<tr>
                    <td>${escSponsor(s.name)}</td>
                    <td><button type="button" class="btn btn-sm btn-primary bo-sponsor-pick-btn" data-pick="${pick}">선택</button></td>
                </tr>`;
            })
            .join('');
    };

    const fetchSponsors = async (page = 1) => {
        if (!searchSponsorsUrl || !sponsorSearchTbody) {
            return;
        }
        sponsorCurrentPage = page;
        const kw = (sponsorSearchKeywordInput?.value || '').trim();
        const url = new URL(searchSponsorsUrl, window.location.origin);
        url.searchParams.set('keyword', kw);
        url.searchParams.set('page', String(page));
        url.searchParams.set('per_page', '10');

        sponsorSearchTbody.innerHTML = '<tr><td colspan="2" class="text-center">조회 중입니다...</td></tr>';
        if (sponsorSearchPagination) {
            sponsorSearchPagination.innerHTML = '';
        }

        try {
            const res = await fetch(url.toString(), { headers: { Accept: 'application/json' } });
            if (!res.ok) {
                throw new Error('스폰서 검색 요청 실패');
            }
            const json = await res.json();
            renderSponsorSearchRows(json.data || []);
            renderSponsorSearchPagination(json.meta || null);
        } catch {
            sponsorSearchTbody.innerHTML = '<tr><td colspan="2" class="text-center">조회 중 오류가 발생했습니다.</td></tr>';
            if (sponsorSearchPagination) {
                sponsorSearchPagination.innerHTML = '';
            }
        }
    };

    document.getElementById('bo-sponsor-add-master-btn')?.addEventListener('click', () => {
        showBsModal(sponsorModalEl);
        fetchSponsors(1);
        sponsorSearchKeywordInput?.focus();
    });

    document.getElementById('bo-sponsor-search-btn')?.addEventListener('click', () => fetchSponsors(1));

    sponsorSearchKeywordInput?.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') {
            return;
        }
        event.preventDefault();
        fetchSponsors(1);
    });

    sponsorSearchPagination?.addEventListener('click', (event) => {
        const link = event.target.closest('.js-sponsor-page');
        if (!link || link.closest('.page-item')?.classList.contains('disabled')) {
            return;
        }
        event.preventDefault();
        fetchSponsors(Number(link.dataset.page || sponsorCurrentPage || 1));
    });

    sponsorModalEl?.addEventListener('click', (e) => {
        const b = e.target.closest('.bo-sponsor-pick-btn');
        if (!b) {
            return;
        }
        let d = {};
        try {
            d = JSON.parse(decodeURIComponent(b.getAttribute('data-pick') || ''));
        } catch {
            return;
        }
        addSponsorRow({ master_id: String(d.id), name: d.name });
        hideBsModal(sponsorModalEl);
    });

    document.getElementById('bo-sponsor-level-filter')?.addEventListener('change', applySponsorLevelFilter);
    document.getElementById('bo-sponsors-body')?.addEventListener('change', (e) => {
        const select = e.target.closest('select[name^="sponsors["][name$="[level]"]');
        if (!select) {
            return;
        }
        applySponsorLevelFilter();
        rebuildAllMainSponsorSelects();
    });
    document.getElementById('bo-sponsors-body')?.addEventListener('click', (e) => {
        if (!e.target.closest('.bo-remove-row-btn')) {
            return;
        }
        setTimeout(() => {
            reindexSponsorRows();
            applySponsorLevelFilter();
            rebuildAllMainSponsorSelects();
        }, 0);
    });
    document.getElementById('bo-speakers-body')?.addEventListener('click', (e) => {
        if (!e.target.closest('.bo-remove-row-btn')) {
            return;
        }
        setTimeout(() => {
            reindexSpeakerRows();
        }, 0);
    });
    form.addEventListener('submit', () => {
        reindexSpeakerRows();
        reindexSponsorRows();
    });
    reindexSpeakerRows();
    reindexSponsorRows();
    applySponsorLevelFilter();

    if (typeof window.initBoardImageFilePreview === 'function') {
        window.initBoardImageFilePreview({
            inputId: 'academic_thumbnail_file',
            previewId: 'academicThumbnailFilePreview',
            removeExistingSelector: '[data-remove-existing-target="thumbnail"]',
            deleteCheckboxId: 'delete_thumbnail',
            existingItemId: 'bo-academic-thumbnail-existing-item',
        });
        window.initBoardImageFilePreview({
            inputId: 'academic_pc_banner_file',
            previewId: 'academicPcBannerFilePreview',
            removeExistingSelector: '[data-remove-existing-target="pc_banner"]',
            deleteCheckboxId: 'delete_pc_banner',
            existingItemId: 'bo-academic-pc-banner-existing-item',
        });
        window.initBoardImageFilePreview({
            inputId: 'academic_greeting_image_file',
            previewId: 'academicGreetingImagePreview',
            removeExistingSelector: '[data-remove-existing-target="greeting_image"]',
            deleteCheckboxId: 'delete_greeting_image',
            existingItemId: 'bo-academic-greeting-existing-item',
        });
        window.initBoardImageFilePreview({
            inputId: 'academic_exhibition_image_file',
            previewId: 'academicExhibitionImageFilePreview',
            removeExistingSelector: '[data-remove-existing-target="exhibition_image"]',
            deleteCheckboxId: 'delete_exhibition_image',
            existingItemId: 'bo-academic-exhibition-existing-item',
        });
    }

    const initAcademicSingleFilePreview = ({ inputId, previewId, deleteCheckboxId, existingItemId }) => {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) {
            return;
        }

        const uploadRoot = input.closest('.board-file-upload');
        const wrapper = input.closest('.board-file-input-wrapper');
        const maxFileSizeMb = Number(input.dataset.maxFileSizeMb || 50);
        const maxBytes = maxFileSizeMb * 1024 * 1024;
        const deleteCheckbox = deleteCheckboxId ? document.getElementById(deleteCheckboxId) : null;
        const existingItem = existingItemId ? document.getElementById(existingItemId) : null;

        const hideExisting = () => {
            if (deleteCheckbox) {
                deleteCheckbox.checked = true;
            }
            existingItem?.classList.add('bo-hidden');
        };
        const showExisting = () => {
            if (deleteCheckbox) {
                deleteCheckbox.checked = false;
            }
            existingItem?.classList.remove('bo-hidden');
        };

        const escapeHtml = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const render = (file) => {
            preview.innerHTML = `
                <div class="board-file-item">
                    <div class="board-file-info">
                        <i class="fas fa-file"></i>
                        <span class="board-file-name">${escapeHtml(file.name)}</span>
                        <span class="board-file-size">(${(file.size / 1024 / 1024).toFixed(2)}MB)</span>
                    </div>
                    <button type="button" class="board-file-remove js-academic-single-file-remove" aria-label="첨부 제거">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`;
        };

        const clear = () => {
            preview.innerHTML = '';
        };

        const processFile = (file) => {
            if (!file) {
                input.value = '';
                clear();
                showExisting();
                return;
            }
            if (file.size > maxBytes) {
                window.alert(`파일은 ${maxFileSizeMb}MB 이하만 업로드할 수 있습니다.`);
                input.value = '';
                clear();
                return;
            }
            render(file);
            hideExisting();
        };

        input.addEventListener('change', (e) => {
            processFile(e.target.files?.[0] ?? null);
        });

        preview.addEventListener('click', (e) => {
            if (!e.target.closest('.js-academic-single-file-remove')) {
                return;
            }
            input.value = '';
            clear();
            showExisting();
        });

        if (wrapper && uploadRoot) {
            wrapper.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.stopPropagation();
                uploadRoot.classList.add('board-file-drag-over');
            });
            wrapper.addEventListener('dragleave', (e) => {
                e.preventDefault();
                e.stopPropagation();
                uploadRoot.classList.remove('board-file-drag-over');
            });
            wrapper.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();
                uploadRoot.classList.remove('board-file-drag-over');
                const file = e.dataTransfer.files?.[0];
                if (!file) {
                    return;
                }
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                processFile(file);
            });
        }
    };

    initAcademicSingleFilePreview({
        inputId: 'academic_event_material_file',
        previewId: 'academicEventMaterialFilePreview',
        deleteCheckboxId: 'delete_event_material',
        existingItemId: 'bo-academic-event-material-existing-item',
    });

    initAcademicSingleFilePreview({
        inputId: 'academic_abstract_book_file',
        previewId: 'academicAbstractBookFilePreview',
        deleteCheckboxId: 'delete_abstract_book',
        existingItemId: 'bo-academic-abstract-book-existing-item',
    });

    form.addEventListener('submit', () => {
        if (typeof window.syncBackofficeCKEditorFields === 'function') {
            window.syncBackofficeCKEditorFields(form);
        }
    });
});
