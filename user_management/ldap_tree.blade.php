<li class="tree-item {{ !empty($children) ? 'has-children' : '' }}">
    @php
        $id = 'node-' . md5($node . $level);
        $checkboxName = 'departments[' . md5($node) . '][name]';
        $parentFieldName = 'departments[' . md5($node) . '][parent]';
    @endphp
    <div class="tree-node" style="--depth: {{ $level }}">


        <input type="checkbox"
               id="{{ $id }}"
               class="parent-checkbox tree-checkbox"
               name="{{ $checkboxName }}"
               value="{{ $node }}"
               data-level="{{ $level }}"
               onchange="handleCheckboxChange(this)">

        <label for="{{ $id }}" class="tree-label">{{ $node }}</label>

        @if (isset($parent))
            <input type="hidden" name="{{ $parentFieldName }}" value="{{ $parent }}">
        @endif
    </div>

    @if (!empty($children))
        <ul class="tree-children">
            @foreach ($children as $childNode => $childChildren)
                @include('admin.content.hierarchy.department.ldap_tree', [
                    'node' => $childNode,
                    'children' => $childChildren,
                    'level' => $level + 1,
                    'parent' => $node
                ])
            @endforeach
        </ul>
    @endif
</li>
