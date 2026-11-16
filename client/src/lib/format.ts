const numberFormat = new Intl.NumberFormat("hu-HU", {
  minimumFractionDigits: 1,
  maximumFractionDigits: 3,
});
const MISSING = "–";

export function formatMeasurement(value: number | null, unit: string): string {
  return value === null ? MISSING : `${numberFormat.format(value)} ${unit}`;
}

export function formatMeasurements(values: number[], unit: string): string {
  return values.length === 0
    ? MISSING
    : values.map((value) => formatMeasurement(value, unit)).join(", ");
}

export function formatDate(isoDate: string): string {
  return `${isoDate.replaceAll("-", ". ")}.`;
}
