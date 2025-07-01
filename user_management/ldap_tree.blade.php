<li class="tree-item {{ !empty($children) || isset($children['groups']) ? 'has-children' : '' }}">
    @php
        $isGroupContainer = $node === 'groups';
    @endphp

    @unless($isGroupContainer)
        <div class="tree-node" style="--depth: {{ $level }}">
            <span class="tree-label ou-label">
                <i class="fas fa-folder-open me-1"></i> {{ $node }}
                @if(isset($children['groups']))
                    <span class="badge bg-primary ms-2">{{ count($children['groups']) }} groups</span>
                @endif
            </span>
        </div>
    @endunless

    @if (!empty($children))
        <ul class="tree-children">
            @foreach ($children as $childNode => $childChildren)
                @if($childNode === 'groups')
                    {{-- Handle groups with radio buttons --}}
                    @foreach ($childChildren as $index => $group)
                        @php
                            $groupId = 'group-' . md5($group[0] . $level);
                        @endphp
                        <li class="tree-item group-item">
                            <div class="tree-node" style="--depth: {{ $level + 1 }}">
                                <input type="radio"
                                       id="{{ $groupId }}"
                                       class="group-radio tree-radio"
                                       name="selected_group"
                                       value="{{ $group[0] }}"
                                       data-ou-path="{{ $node }}" {{-- Store parent OU --}}
                                       data-level="{{ $level + 1 }}"
                                       @if($index === 0) checked @endif>

                                <label for="{{ $groupId }}" class="tree-label">
                                    <i class="fas fa-users me-1"></i> {{ $group[0] }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                @else
                    {{-- Normal department/node --}}
                    @include('admin.content.configure.user_management.ldap_tree', [
                        'node' => $childNode,
                        'children' => $childChildren,
                        'level' => $level + 1,
                        'parent' => $node
                    ])
                @endif
            @endforeach
        </ul>
    @endif
</li>
