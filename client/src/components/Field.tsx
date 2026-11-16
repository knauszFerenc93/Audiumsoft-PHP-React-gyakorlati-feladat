import type { ReactNode } from "react";

export const INPUT_CLASS =
  "w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-sky-600 focus:ring-2 focus:ring-sky-200 focus:outline-none invalid:border-red-400 invalid:bg-red-50 invalid:text-red-700";

export function Field({
  id,
  label,
  children,
}: {
  id: string;
  label: string;
  children: ReactNode;
}) {
  return (
    <div className="flex flex-col gap-1">
      <label
        htmlFor={id}
        className="text-xs font-semibold tracking-wide text-slate-500 uppercase"
      >
        {label}
      </label>
      {children}
    </div>
  );
}
