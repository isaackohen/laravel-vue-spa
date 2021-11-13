@if($column['show_modifiers'])
    <div class="form-group col-md-1 mb-1 px-1">
    <label class="mb-1">{{ucfirst($label)}}</label>
@endif
	<input
        type="{{$type}}"
		min="0"
		wire:model="columns.{{ $column_index }}.modifiers.{{$label}}"
		name="columns[{{ $column_index }}][modifiers][{{$label}}]"
		class="form-control "
		@if(in_array($label, $invalid_modifiers))
			readonly
		@endif
        />
@if($column['show_modifiers'])
</div>
@endif
