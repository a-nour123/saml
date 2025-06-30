@if ($node === 'groups' && is_array($children))
    @foreach ($children as $group)
        @php
            $groupName = $group[0] ?? 'Unknown Group';
            $groupId = 'group-' . md5($groupName . $level);
            $checkboxName = 'departments[' . md5($groupName) . '][name]';
            $parentFieldName = 'departments[' . md5($groupName) . '][parent]';
        @endphp

        <li class="tree-item">
            <div class="tree-node" style="--depth: {{ $level }}">
                <input type="checkbox"
                       id="{{ $groupId }}"
                       class="parent-checkbox tree-checkbox"
                       name="{{ $checkboxName }}"
                       value="{{ $groupName }}"
                       data-level="{{ $level }}"
                       onchange="handleCheckboxChange(this)">

                <label for="{{ $groupId }}" class="tree-label">{{ $groupName }}</label>

                @if (isset($parent))
                    <input type="hidden" name="{{ $parentFieldName }}" value="{{ $parent }}">
                @endif
            </div>
        </li>
    @endforeach
@elseif (is_array($children))
    <li class="tree-item {{ array_key_exists('groups', $children) ? 'has-children' : '' }}">
        <div class="tree-node" style="--depth: {{ $level }}">
            <label class="tree-label">{{ $node }}</label>
        </div>

        <ul class="tree-children">
            @foreach ($children as $childNode => $childChildren)
                @include('admin.content.configure.user_management.ldap_tree', [
                    'node' => $childNode,
                    'children' => $childChildren,
                    'level' => $level + 1,
                    'parent' => $node
                ])
            @endforeach
        </ul>
    </li>
@endif
