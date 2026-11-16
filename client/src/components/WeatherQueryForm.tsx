import { useState, type ChangeEvent, type FormEvent } from "react";
import { fromDateBounds, toDateBounds } from "../lib/dateBounds.ts";
import type { Option, WeatherQuery } from "../types.ts";
import { Field, INPUT_CLASS } from "./Field.tsx";

type WeatherQueryFormProps = {
  targetLabel: string;
  options: Option[];
  loading: boolean;
  onSubmit: (query: WeatherQuery) => void;
};

export function WeatherQueryForm({
  targetLabel,
  options,
  loading,
  onSubmit,
}: WeatherQueryFormProps) {
  const [query, setQuery] = useState<WeatherQuery>({
    target: "",
    from: "2023-01-01",
    to: "2023-01-31",
  });
  const target = query.target || (options[0]?.value ?? "");

  const update =
    (key: keyof WeatherQuery) =>
    (event: ChangeEvent<HTMLInputElement | HTMLSelectElement>) =>
      setQuery((current) => ({ ...current, [key]: event.target.value }));

  const submit = (event: FormEvent) => {
    event.preventDefault();
    onSubmit({ ...query, target });
  };

  return (
    <form
      onSubmit={submit}
      className="grid gap-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:grid-cols-[2fr_1fr_1fr_auto] sm:items-end"
    >
      <Field id="target" label={targetLabel}>
        <select
          id="target"
          value={target}
          onChange={update("target")}
          className={INPUT_CLASS}
        >
          {options.map((option) => (
            <option key={option.value} value={option.value}>
              {option.label}
            </option>
          ))}
        </select>
      </Field>
      <Field id="from" label="Kezdődátum">
        <input
          id="from"
          type="date"
          required
          {...fromDateBounds(query.to)}
          value={query.from}
          onChange={update("from")}
          className={INPUT_CLASS}
        />
      </Field>
      <Field id="to" label="Végdátum">
        <input
          id="to"
          type="date"
          required
          {...toDateBounds(query.from)}
          value={query.to}
          onChange={update("to")}
          className={INPUT_CLASS}
        />
      </Field>
      <button
        type="submit"
        disabled={loading || target === ""}
        className="cursor-pointer rounded-lg bg-sky-700 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:bg-slate-400"
      >
        {loading ? "Betöltés…" : "Lekérdezés"}
      </button>
    </form>
  );
}
