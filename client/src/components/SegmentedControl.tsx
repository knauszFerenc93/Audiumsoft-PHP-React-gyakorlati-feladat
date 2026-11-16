type Segment<T extends string> = {
  id: T;
  label: string;
};

type SegmentedControlProps<T extends string> = {
  label: string;
  segments: Segment<T>[];
  active: T;
  onSelect: (id: T) => void;
};

export function SegmentedControl<T extends string>({
  label,
  segments,
  active,
  onSelect,
}: SegmentedControlProps<T>) {
  return (
    <div
      role="tablist"
      aria-label={label}
      className="inline-flex rounded-xl bg-slate-200 p-1"
    >
      {segments.map((segment) => (
        <button
          key={segment.id}
          type="button"
          role="tab"
          aria-selected={segment.id === active}
          onClick={() => onSelect(segment.id)}
          className="cursor-pointer rounded-lg px-5 py-2 text-sm font-semibold text-slate-600 transition hover:text-sky-800 aria-selected:bg-white aria-selected:text-sky-800 aria-selected:shadow-sm"
        >
          {segment.label}
        </button>
      ))}
    </div>
  );
}
