<?php
  // -----------------------
  // Backpack ChartJS Widget
  // -----------------------
  // Uses:
  // - Backpack\CRUD\app\Http\Controllers\ChartController
  // - https://github.com/ConsoleTVs/Charts
  // - https://github.com/chartjs/Chart.js

  $controller = new $widget['controller'];
  $chart = $controller->chart;
  $path = $controller->getLibraryFilePath();

  // defaults
  $widget['wrapper']['class'] = $widget['wrapper']['class'] ?? $widget['wrapperClass'] ?? 'col-sm-6 col-md-4';
?>

<?php echo $__env->renderWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_start', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
  <div class="<?php echo e($widget['class'] ?? 'card'); ?>">
    <?php if(isset($widget['content']['header'])): ?>
    <div class="card-header"><?php echo $widget['content']['header']; ?></div>
    <?php endif; ?>
    <div class="card-body">

      <?php echo $widget['content']['body'] ?? ''; ?>


      <div class="card-wrapper">
        <?php echo $chart->container(); ?>

      </div>

    </div>
  </div>
<?php echo $__env->renderWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_end', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

<?php $__env->startPush('after_scripts'); ?>
  <?php if(is_array($path)): ?>
    <?php $__currentLoopData = $path; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $string): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <script src="<?php echo e($string); ?>" charset="utf-8"></script>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  <?php elseif(is_string($path)): ?>
    <script src="<?php echo e($path); ?>" charset="utf-8"></script>
  <?php endif; ?>

  <?php echo $chart->script(); ?>


<?php $__env->stopPush(); ?>
<?php /**PATH /home/acakw/public_html/booking/vendor/backpack/crud/src/resources/views/base/widgets/chart.blade.php ENDPATH**/ ?>