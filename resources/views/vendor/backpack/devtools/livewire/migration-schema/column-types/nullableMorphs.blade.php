<div class="form-group col-md-3 mb-1 px-1">
	<label class="mb-1">Morphable</label>
	<input
		type="text"
		wire:model="columns.{{ $column_index }}.args.morphable"
		name="columns[{{ $column_index }}][args][morphable]"
		class="form-control " />
        @if(!is_int($columns[$column_index]['has_relationship']))
            <small class="text-danger">Morph relation missing.</small><a class="add-relationship" target="_blank" href="#" wire:click.prevent="createMorphToRelationFromColumn({{$column_index}})">+ Add </a></small>
        @endif
</div>

